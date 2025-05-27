@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Platform Overview Banner -->
    <div class="bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg shadow-sm mb-6 text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">AliExpress Product Management Dashboard</h1>
                <p class="text-emerald-100">Monitor your AliExpress products, categories, and user interactions</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ number_format($aliexpress_stats['total_products']) }}</div>
                <div class="text-emerald-100">Total AliExpress Products</div>
            </div>
        </div>
    </div>

    <!-- AliExpress Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- AliExpress Products Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border-l-4 border-emerald-500">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-emerald-100 p-3 mr-4">
                        <i class="fas fa-shopping-cart text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ number_format($aliexpress_stats['total_products']) }}</h3>
                        <p class="text-sm text-gray-500">AliExpress Products</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AliExpress Categories Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border-l-4 border-blue-500">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <i class="fas fa-tags text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $aliexpress_stats['total_categories'] }}</h3>
                        <p class="text-sm text-gray-500">AliExpress Categories</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border-l-4 border-indigo-500">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-indigo-100 p-3 mr-4">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $totalUsers }}</h3>
                        <p class="text-sm text-gray-500">Total Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AliExpress Views Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border-l-4 border-purple-500">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-purple-100 p-3 mr-4">
                        <i class="fas fa-eye text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ number_format($aliexpress_stats['total_views']) }}</h3>
                        <p class="text-sm text-gray-500">AliExpress Views</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Comparison -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- AliExpress Summary -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-emerald-50">
                <h2 class="text-lg font-semibold text-emerald-800 flex items-center">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    AliExpress Platform
                </h2>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Products</span>
                    <span class="font-semibold text-emerald-600">{{ number_format($aliexpress_stats['total_products']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Categories</span>
                    <span class="font-semibold text-emerald-600">{{ $aliexpress_stats['total_categories'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Views</span>
                    <span class="font-semibold text-emerald-600">{{ number_format($aliexpress_stats['total_views']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Saves</span>
                    <span class="font-semibold text-emerald-600">{{ number_format($aliexpress_stats['total_saves']) }}</span>
                </div>
            </div>
        </div>

        <!-- Amazon Bot Summary -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-orange-50">
                <h2 class="text-lg font-semibold text-orange-800 flex items-center">
                    <i class="fab fa-amazon mr-2"></i>
                    Amazon Bot Platform
                </h2>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Products</span>
                    <span class="font-semibold text-orange-600">{{ number_format($amazon_stats['total_products']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Categories</span>
                    <span class="font-semibold text-orange-600">{{ $amazon_stats['total_categories'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Views</span>
                    <span class="font-semibold text-orange-600">{{ number_format($amazon_stats['total_views']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Saves</span>
                    <span class="font-semibold text-orange-600">{{ number_format($amazon_stats['total_saves']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- AliExpress Activity Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- AliExpress Product Activity Chart -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden lg:col-span-2">
            <div class="p-4 border-b border-gray-200 bg-emerald-50">
                <h2 class="text-lg font-semibold text-emerald-800 flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    AliExpress Product Activity
                </h2>
            </div>
            <div class="p-4">
                <canvas id="aliexpressActivityChart" height="250"></canvas>
            </div>
        </div>

        <!-- Recently Viewed AliExpress Products -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-emerald-50">
                <h2 class="text-lg font-semibold text-emerald-800">AliExpress Views Today</h2>
                <span class="text-sm text-emerald-600 font-medium">{{ $aliexpress_stats['viewed_today'] }} views</span>
            </div>
            <div class="overflow-y-auto max-h-[320px]">
                @forelse($aliexpressRecentlyViewed as $product)
                    <div class="p-3 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex items-center">
                            <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="w-10 h-10 rounded-md object-cover mr-3">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 truncate" style="max-width: 200px;">{{ $product->title }}</h3>
                                <div class="flex items-center text-xs text-gray-500">
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">{{ $product->views_count }} view(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-eye text-gray-300 text-2xl mb-2"></i>
                        <p>No AliExpress products viewed today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- AliExpress Analytics & Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- AliExpress Category Distribution Chart -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-emerald-50">
                <h2 class="text-lg font-semibold text-emerald-800 flex items-center">
                    <i class="fas fa-chart-pie mr-2"></i>
                    AliExpress Categories
                </h2>
            </div>
            <div class="p-4">
                <canvas id="aliexpressCategoryChart" height="250"></canvas>
            </div>
        </div>

        <!-- AliExpress Top Products -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-emerald-50">
                <h2 class="text-lg font-semibold text-emerald-800 flex items-center">
                    <i class="fas fa-star mr-2"></i>
                    Top AliExpress Products
                </h2>
            </div>
            <div class="p-4">
                <canvas id="aliexpressTopProductsChart" height="250"></canvas>
            </div>
        </div>

        <!-- Recently Saved AliExpress Products -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-emerald-50">
                <h2 class="text-lg font-semibold text-emerald-800">AliExpress Saves Today</h2>
                <span class="text-sm text-emerald-600 font-medium">{{ $aliexpress_stats['saved_today'] }} saves</span>
            </div>
            <div class="overflow-y-auto max-h-[320px]">
                @forelse($aliexpressRecentlySaved as $product)
                    <div class="p-3 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex items-center">
                            <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="w-10 h-10 rounded-md object-cover mr-3">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 truncate" style="max-width: 200px;">{{ $product->title }}</h3>
                                <div class="flex items-center text-xs text-gray-500">
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">{{ $product->saves_count }} save(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-bookmark text-gray-300 text-2xl mb-2"></i>
                        <p>No AliExpress products saved today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // AliExpress Product Activity Chart - Line Chart
        const aliexpressActivityCtx = document.getElementById('aliexpressActivityChart').getContext('2d');
        const aliexpressActivityChart = new Chart(aliexpressActivityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($aliexpressActivityDates) !!},
                datasets: [
                    {
                        label: 'AliExpress Views',
                        data: {!! json_encode($aliexpressViewsData) !!},
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'AliExpress Saves',
                        data: {!! json_encode($aliexpressSavesData) !!},
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });

        // AliExpress Category Distribution Chart - Doughnut
        const aliexpressCategoryCtx = document.getElementById('aliexpressCategoryChart').getContext('2d');
        const aliexpressCategoryChart = new Chart(aliexpressCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($aliexpressCategoryLabels) !!},
                datasets: [{
                    data: {!! json_encode($aliexpressCategoryData) !!},
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                }
            }
        });

        // AliExpress Top Products Chart - Bar Chart
        const aliexpressTopProductsCtx = document.getElementById('aliexpressTopProductsChart').getContext('2d');
        const aliexpressTopProductsChart = new Chart(aliexpressTopProductsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($aliexpressTopProductLabels) !!},
                datasets: [{
                    label: 'Views This Month',
                    data: {!! json_encode($aliexpressTopProductData) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
