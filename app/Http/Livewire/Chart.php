<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Chart extends Component
{
    public array $dataset = [];
    public array $labels = [];
    // public Organization $organization;

    protected $listeners = [
        'organization-selected' => 'organizationSelected',
    ];

    public function organizationSelected(string $uuid)
    {
        // $this->organization = Organization::whereUuid($uuid)->first();

        $labels = $this->getLabels();

        $dataset = [
            [
                'label' => 'Logged In',
                'backgroundColor' => 'rgba(15,64,97,255)',
                'borderColor' => 'rgba(15,64,97,255)',
                'data' => $this->getRandomData(),
            ],
        ];

        $this->emit('updateChart', [
            'datasets' => $dataset,
            'labels' => $labels,
        ]);
    }

    public function mount()
    {
        $this->labels[] = $this->getLabels();

        $this->dataset = [
            [
                'label' => 'Logged In',
                'backgroundColor' => 'rgba(75,85,99,255)',
                'borderColor' => 'rgba(0,0,0,255)',
                'data' => $this->getRandomData(),
            ],
        ];
    }

    private function getLabels()
    {
        $labels = [];
        for ($i = 0; $i < 12; $i++) {
            $labels[] = now()->subMonths($i)->format('M');
        }
        return $labels;
    }

    private function getRandomData()
    {
        $data = [];
        for ($i = 0; $i < count($this->getLabels()); $i++) {
            $data[] = rand(10, 100);
        }
        return $data;
    }

    public function render()
    {
        return view('livewire.chart');
    }
}