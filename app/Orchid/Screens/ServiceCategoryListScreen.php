<?php

namespace App\Orchid\Screens;

use App\Models\ServiceCategory;
use Orchid\Screen\Actions\Link;
// use Orchid\Screen\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;

class ServiceCategoryListScreen extends Screen
{

    public $name = 'Service Categories';
    public $description = 'List of all service categories';
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'serviceCategories' => ServiceCategory::with('translations')->latest()->get(),
        ];
    }
    /** 
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        // Validate form data, save offer to database, etc.
        $validated = $request->validate([
            'serviceCategory.en.title' => 'required|max:255',
            'serviceCategory.kn.title' => 'required|max:255',
        ]);
        $serviceCategory = ServiceCategory::create();

        foreach ($validated['serviceCategory'] as $locale => $fields) {
            // Skip if it's not actually a locale array (like 'name')
            if (in_array($locale, ['en', 'kn'])) {
                $serviceCategory->translations()->create([
                    'locale' => $locale,
                    'title' => $fields['title'],
                ]);
            }
        }

        //    $serviceCategory = new ServiceCategory();
        //    $serviceCategory->title = $request->input('serviceCategory.title');
        //    $serviceCategory->save();
    }
    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Service Category List';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create New')
                ->modal('createServiceCategoryModal')
                ->method('create')
                ->icon('plus')

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('serviceCategories', [
                TD::make('id', 'ID')->sort(),

                // TD::make('title', 'Title')
                //     ->render(fn(ServiceCategory $cat) => e($cat->title)),

                TD::make('title_en', 'Title (EN)')
                    ->render(fn(ServiceCategory $offer) => $offer->getTitle('en')),

                TD::make('title_kn', 'Title (KN)')
                    ->render(fn(ServiceCategory $offer) => $offer->getTitle('kn')),

                TD::make('image_path', 'Image')
                    ->render(
                        fn(ServiceCategory $cat) =>
                        $cat->image_path
                        ? "<img src='" . asset($cat->image_path) . "' width='60' />"
                        : '—'
                    )->width('100px'),

                TD::make('active', 'Active')
                    ->render(
                        fn(ServiceCategory $cat) =>
                        $cat->active ? '✅' : '❌'
                    ),

                TD::make('Actions')
                    ->align(TD::ALIGN_CENTER)
                    ->width('200px')
                    ->render(
                        fn(ServiceCategory $cat) =>
                        Link::make('Edit')
                            ->route('platform.serviceCategories.edit', $cat->id)
                            ->icon('pencil')
                        . ' ' .
                        Button::make('Delete')
                            ->icon('trash')
                            ->method('remove', ['id' => $cat->id])
                            ->confirm('Are you sure you want to delete this category?')
                    ),
            ]),
            Layout::modal('createServiceCategoryModal', Layout::tabs([
                'General' => [
                    Layout::rows([
                        Input::make('serviceCategory.name')
                            ->title('DOnt enter this is not a real name'),
                            // ->required(),
                    ]),
                ],
                'English' => [
                    Layout::rows([
                        Input::make('serviceCategory.en.title')
                            ->title('Title (English)')
                            ->required(),
                    ]),
                ],
                'Kannada' => [
                    Layout::rows([
                        Input::make('serviceCategory.kn.title')
                            ->title('Title (Kannada)')
                            ->required(),
                    ]),
                ],




                // Layout::rows([
                //     Input::make('serviceCategory.title')
                //         ->title('Title')
                //         ->required(),
                // ])
            ])
            )
        ];
    }

    public function remove(ServiceCategory $serviceCategory)
    {
        // delete file if you want cleanup
        if ($serviceCategory->image_path && \Storage::disk('public')->exists($serviceCategory->image_path)) {
            \Storage::disk('public')->delete($serviceCategory->image_path);
        }

        $serviceCategory->delete();

        Toast::info('Category deleted successfully.');
    }
}
