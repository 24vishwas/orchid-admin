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
        return [
            'serviceCategory' => $serviceCategory,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->serviceCategory->exists
        ? 'Edit Service Category'
        : 'Create Service Category';
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
                ->canSee($this->serviceCategory->exists),
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
                Input::make('serviceCategory.title')
                    ->title('Title')
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
        $request->validate([
            'serviceCategory.title' => ['required', 'string', 'max:255'],
            // Note: Picture handles upload; validation here is optional
        ]);

        $data = $request->get('serviceCategory');
        $serviceCategory->fill($data)->save();

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

