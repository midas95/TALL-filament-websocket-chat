@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        const chart = new Chart(
            document.getElementById('chart'), {
                type: 'line',
                data: {
                    labels: @json($labels)[0],
                    datasets: @json($dataset)
                },
                options: {
                    transitions: {
                        show: {
                            animations: {
                            x: {
                                from: 0
                            },
                            y: {
                                from: 0
                            }
                            }
                        },
                        hide: {
                            animations: {
                            x: {
                                to: 0
                            },
                            y: {
                                to: 0
                            }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },

                    },
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                color: 'green',
                            },
                            ticks: {
                                color: 'blue',
                            }
                        },
                        y: {
                            stacked: true,
                            grid: {
                                color: 'green',
                            },
                            ticks: {
                                color: 'blue',
                            }
                        }
                    }
                },
            }
        );
        Livewire.on('updateChart', data => {
            chart.data = data;
            chart.update();
        });
    </script>
@endpush

<div class=" bg-gray-600 p-4 mx-4 mt-4">
    <canvas id="chart"></canvas>
</div>
