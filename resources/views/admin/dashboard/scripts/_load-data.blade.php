<script>
    $(document).ready(function() {
        loadData()
    })

    $('body').on('click', '.pagination a', function(event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);

        loadData()
    });

    $('#filter').click(function(e) {
        e.preventDefault()
        $('#hidden_page').val(1);
        loadData()
    })

    $('#reset').click(function(e) {
        e.preventDefault();

        $("#search-date").val('');

        loadData()
    })


    function loadData() {

        // const data = $('#data-body')

        const date = $("#search-date").val();
        const [startDate, endDate] = date.split(" - ");

        $.ajax({
            url: `{{ route('admin.dashboard.filter') }}`,
            type: 'GET',
            data: {
                startDate: startDate,
                endDate: endDate,
            },
            beforeSend: function() {
                // data.html(`<tr>
                //     <td class="text-center" colspan="7">
                //         <div class="d-flex justify-content-center">
                //         <div class="spinner-border" role="status">
                //             <span class="sr-only">Loading...</span>
                //         </div>
                //     </div>
                //         </td>
                //     </tr>`)
            },
            success: function(response) {
                // console.log(response);
                // Render stats
                const statsContainer = $('.stats-container'); // Update with your container class or id
                statsContainer.empty(); // Clear existing stats

                response.stats.forEach(function(item) {
                    const statHtml = `
                <div class="col-lg-4" style="display: ${item.show ? 'block' : 'none'}">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-fill">
                                    <p class="mb-1 fs-5 fw-semibold text-default">${item.value}</p>
                                    <p class="mb-0 text-muted">${item.title}</p>
                                    ${item.link ? '<p class="mb-0 fs-11"><a href="javascript:void(0);" class="text-success text-decoration-underline">View All</a></p>' : ''}
                                </div>
                                <div class="ms-2">
                                    <span class="avatar ${item.color} rounded-circle fs-20"><i class="${item.icon}"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                    statsContainer.append(statHtml);
                });

                // Render top-selling services
                const topSellingTableBody = $('.top-selling');
                topSellingTableBody.empty(); // Clear existing rows

                if (response.topSellingServices.length > 0) {
                    response.topSellingServices.forEach(function(service, index) {
                        const serviceHtml = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <x-package-title title="${service.service.name}" image="${service.service.image_url}" price="${service.service.price}" />
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">$${parseFloat(service.service.price).toFixed(2)}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">$${parseFloat(service.total_amount_sold).toFixed(2)}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">${service.total_sales_count}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">${parseFloat(service.service.bv_amount).toFixed(2)} BV</span>
                        </td>
                    </tr>`;
                        topSellingTableBody.append(serviceHtml);
                    });
                } else {
                    topSellingTableBody.append(
                        '<tr><td colspan="6" class="text-center text-warning">No top-selling services found.</td></tr>'
                    );
                }


                if (response.salesOvertime.total_amounts.length > 0) {
                    salesPerMonthStats(response.salesOvertime)
                }

                salesPerService(response.salesPerService)

                console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.log(xhr.responseJSON)
            }
        });
    }

    function salesPerMonthStats(data) {
        var options = {
            series: [{
                name: 'Sales',
                data: data.total_amounts
            }],
            chart: {
                height: 320,
                type: 'bar',
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return "$" + val;
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#8c9097"]
                }
            },
            colors: ["#845adf"],
            xaxis: {
                categories: data.labels,
                position: 'top',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                },
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-xaxis-label',
                    },
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: false,
                    formatter: function(val) {
                        return val + "%";
                    }
                }

            },
            title: {
                text: 'Monthly Sales',
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#444'
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#earnings"), options);
        chart.render();
    }

    function salesPerService(data) {

        var optionsSales = {
            series: [], // Empty series initially
            chart: {
                height: 320,
                type: 'area' // Area chart
            },
            colors: [], // Empty colors initially (will be dynamically updated)
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            xaxis: {
                categories: [], // Empty categories initially (filled with dates later)
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-xaxis-label',
                    },
                }
            },
            yaxis: {
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-xaxis-label',
                    },
                }
            },
            tooltip: {
                x: {
                    format: 'dd/MM' // Format tooltip to show date properly
                },
            }
        };

        // Initialize the chart
        var chartSales = new ApexCharts(document.querySelector("#sales-per-service"), optionsSales);
        chartSales.render();

        var seriesData = [];
        var categories = []; // To hold the unique days
        var colors = [];

        // Pre-defined colors, or you can add more
        var availableColors = ["#845adf", "#23b7e5", "#FF4560", "#775DD0", "#00E396", "#FEB019", "#FF4560", "#775DD0",
            "#FF6347"
        ];

        // If response.salesPerService is an object, use Object.keys()
        Object.keys(data).forEach(function(serviceId, index) {
            var sale = data[serviceId];
            var dailySales = [];

            // Loop through the dates of the month (or range)
            for (var date in sale.daily_sales) {
                if (!categories.includes(date)) {
                    categories.push(date);
                }
                dailySales.push(sale.daily_sales[date] || 0);
            }

            var color = availableColors[index % availableColors.length];

            seriesData.push({
                name: sale.service_name,
                data: dailySales
            });

            colors.push(color);
        });

        // Sort categories (dates)
        categories.sort();

        chartSales.updateOptions({
            xaxis: {
                categories: categories
            },
            series: seriesData,
            colors: colors
        });
    }

    function userByCountry(markers) {
        var map = new jsVectorMap({
            map: 'world_merc',
            selector: '#users-map',
            markersSelectable: true,

            onMarkerSelected(index, isSelected, selectedMarkers) {
                console.log(index, isSelected, selectedMarkers);
            },

            labels: {
                markers: {
                    render: function(marker) {
                        return marker.name
                    },
                },
            },

            markers: markers,
            markerStyle: {
                hover: {
                    stroke: "#DDD",
                    strokeWidth: 3,
                    fill: '#FFF'
                },
                selected: {
                    fill: '#ff525d'
                }
            },
            markerLabelStyle: {
                initial: {
                    fontFamily: 'Poppins',
                    fontSize: 13,
                    fontWeight: 500,
                    fill: '#35373e',
                },
            },
        });
    }
</script>
