<?php

namespace App\Orchid\Screens;

use Orchid\Alert\Alert;
use Orchid\Alert\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
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
            'offers' => Offer::with('translations')->latest()->get(),
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
            'offer.name' => 'required|max:255',
            'offer.en.title' => 'required|max:255',
            'offer.kn.title' => 'required|max:255',
        ]);
        $offer = Offer::create([
            'name' => $validated['offer']['name'],
        ]);
        // $offer = new Offer();
        // $offer->name = $request->input('offer.name');
        // $offer->title = $request->input('offer.title');
        // $offer->save();
        foreach ($validated['offer'] as $locale => $fields) {
            // Skip if it's not actually a locale array (like 'name')
            if (in_array($locale, ['en', 'kn'])) {
                $offer->translations()->create([
                    'locale' => $locale,
                    'title' => $fields['title'],
                ]);
            }
        }
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

    public function asyncGetOffer(Offer $offer): array
    {
        return [
            'offer' => [
                'id' => $offer->id,
                'name' => $offer->name,
                'en' => [
                    'title' => $offer->getTitle('en'),
                ],
                'kn' => [
                    'title' => $offer->getTitle('kn'),
                ],
            ],
        ];
    }

    public function saveOffer(Offer $offer, Request $request)
    {
        $validated = $request->validate([
            'offer.name' => 'required|max:255',
            'offer.en.title' => 'required|max:255',
            'offer.kn.title' => 'required|max:255',
        ]);
        // Update main offer
        $offer->update([
            'name' => $validated['offer']['name'],
        ]);
        // $offer->translations()->updateOrCreate(
        //     ['locale' => 'en'],
        //     ['title' => $validated['offer']['title_en']]
        // );

        // $offer->translations()->updateOrCreate(
        //     ['locale' => 'kn'],
        //     ['title' => $validated['offer']['title_kn']]
        // );
        foreach ($validated['offer'] as $locale => $fields) {
            if (in_array($locale, ['en', 'kn'])) {
                $offer->translations()->updateOrCreate(
                    ['locale' => $locale], // condition
                    ['title' => $fields['title'],
                    'offer_id' => $offer->id] // values to update/create
                );
            }
        }

        $alert = Alert();
        $alert->info('Offer updated successfully!');
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
                TD::make('title_en', 'Title (EN)')
                    ->render(fn(Offer $offer) => $offer->getTitle('en')),

                TD::make('title_kn', 'Title (KN)')
                    ->render(fn(Offer $offer) => $offer->getTitle('kn')),

                TD::make('Actions')
                    ->alignRight()
                    ->render(
                        fn(Offer $offer) =>
                        Group::make([
                            ModalToggle::make('Edit Offer')
                                ->modal('editOfferModal')   // modal name
                                ->modalTitle('Edit Offer')
                                ->method('saveOffer')
                                ->icon('pencil')
                                ->asyncParameters([
                                    'offer' => $offer->id,  // pass offer id to modal
                                ]),

                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('After deleting, the offer will be gone forever.')
                                ->method('delete', ['offer' => $offer->id]),
                        ])
                    ),
            ]),
            Layout::modal('offerModal', Layout::tabs([
                'General' => [
                    Layout::rows([
                        Input::make('offer.name')
                            ->title('Name')
                            ->required(),
                    ]),
                ],
                'English' => [
                    Layout::rows([
                        Input::make('offer.en.title')
                            ->title('Title (English)')
                            ->required(),
                    ]),
                ],
                'Kannada' => [
                    Layout::rows([
                        Input::make('offer.kn.title')
                            ->title('Title (Kannada)')
                            ->required(),
                    ]),
                ],
            ]))
                ->title('Create Offer')
                ->applyButton('Add Offer'),

            Layout::modal('editOfferModal', Layout::rows([
                Input::make('offer.id')->type('hidden'),
                Input::make('offer.name')->title('Name')->required(),
                Input::make('offer.en.title')
                    ->title('Title (EN)')
                    ->required(),

                Input::make('offer.kn.title')
                    ->title('Title (KN)')
                    ->required(),
            ]))->async('asyncGetOffer'),
        ];
    }
}
