<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use App\Models\Offer;
use Illuminate\Http\Request;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class OfferScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
         return [
        'offers' => Offer::latest()->get(),
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
    $request->validate([
        'offer.name' => 'required|max:255',
        'offer.title' => 'required|max:255',
    ]);

    $offer = new Offer();
    $offer->name = $request->input('offer.name');
    $offer->title = $request->input('offer.title');
    $offer->save();
}

/**
 * @param Offer $offer
 *
 * @return void
 */
public function delete(Offer $offer)
{
    $offer->delete();
}

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Offers List';
    }


/**
 * The description is displayed on the user's screen under the heading
 */
public function description(): ?string
{
    return 'Offers Quickstart';
}


    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
{
    return [
        ModalToggle::make('Add Offer')
            ->modal('offerModal')
            ->method('create')
            ->icon('plus'),
    ];
}

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
    return [
                Layout::table('offers', [
            TD::make('name'),
            TD::make('title'),
             TD::make('Actions')
        ->alignRight()
        ->render(function (Offer $offer) {
            return Button::make('Delete Offer')
                ->confirm('After deleting, the offer will be gone forever.')
                ->method('delete', ['offer' => $offer->id]);
        }),
        ]),
        Layout::modal('offerModal', Layout::rows([
            Input::make('offer.name')
                ->title('Name')
                ->placeholder('Enter offer name')
                ->help('The name of the offer to be created.'),
            Input::make('offer.title')
                ->title('Title')
                ->placeholder('Enter offer Title')
                ->help('This title will be displayed on the offer.'),
        ]))
            ->title('Create Offer')
            ->applyButton('Add Offer'),
            
    ];
}
}
