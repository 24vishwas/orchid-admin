<?php

namespace App\Orchid\Screens;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Orchid\Alert\Alert;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Switcher;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;

class ServiceCategoryEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public $serviceCategory;


    public function query(ServiceCategory $serviceCategory): array
    {
        // return [
        //     'serviceCategory' => $serviceCategory,
        // ];
        return [
            'serviceCategory' => [
                'id' => $serviceCategory->id,
                'image_path' => $serviceCategory->image_path,
                'active' => $serviceCategory->active,
                'en' => [
                    'title' => $serviceCategory->getTitle('en'),
                ],
                'kn' => [
                    'title' => $serviceCategory->getTitle('kn'),
                ],
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Edit Service Category';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('save'),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                // ->canSee($this->resource->exists),
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
            Layout::rows([
                Input::make('serviceCategory.en.title')
                    ->title('Title (EN)')
                    ->required(),
                Input::make('serviceCategory.kn.title')
                    ->title('Title (KN)')
                    ->required(),

                Switcher::make('serviceCategory.active')
                    ->title('Active')
                    ->sendTrueOrFalse(),

                Picture::make('serviceCategory.image_path')
                    ->title('Image')
                    ->storage('public')      // saves to storage/app/public
                    ->targetRelativeUrl()            
                    ->acceptedFiles('image/*')         // limit to images
                    ->help('Upload a category image.'),
            ]),
        ];
    }
    public function save(Request $request, ServiceCategory $serviceCategory)
    {
        // Validate (optional but recommended)
        $validated = $request->validate([
            'serviceCategory.en.title' => 'required|string|max:255',
            'serviceCategory.kn.title' => 'required|string|max:255',
            'serviceCategory.image_path' => 'nullable|string',
            'serviceCategory.active' => 'nullable|boolean',
            // Note: Picture handles upload; validation here is optional
        ]);

        $serviceCategory->update([
            'image_path' => $validated['serviceCategory']['image_path'],
            'active' => $validated['serviceCategory']['active'],
        ]);
        foreach ($validated['serviceCategory'] as $locale => $fields) {
            if (in_array($locale, ['en', 'kn'])) {
                $serviceCategory->translations()->updateOrCreate(
                    ['locale' => $locale], // condition
                    ['title' => $fields['title'],
                    'service_category_id' => $serviceCategory->id] // values to update/create
                );
            }
        }

        // $data = $request->get('serviceCategory');
        // $serviceCategory->fill($data)->save();

        alert()->info('Service category saved successfully.');
        return redirect()->route('platform.serviceCategories');
    }

    public function remove(ServiceCategory $serviceCategory)
    {
        // Optional: delete old file from storage if you want to clean up
        if ($serviceCategory->image_path && \Storage::disk('public')->exists($serviceCategory->image_path)) {
            \Storage::disk('public')->delete($serviceCategory->image_path);
        }

        $serviceCategory->delete();
        alert()->info('Service category removed.');
        return redirect()->route('platform.service-categories');
    }
}

