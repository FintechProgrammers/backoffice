<script>
    /* semi circular gauge */
    var optionsCircl = {
        series: [76],
        chart: {
            type: 'radialBar',
            height: 320,
            offsetY: -20,
            sparkline: {
                enabled: true
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: -90,
                endAngle: 90,
                track: {
                    background: "#fff",
                    strokeWidth: '97%',
                    margin: 5, // margin is in pixels
                    dropShadow: {
                        enabled: false,
                        top: 2,
                        left: 0,
                        color: '#999',
                        opacity: 1,
                        blur: 2
                    }
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        offsetY: -2,
                        fontSize: '22px'
                    }
                }
            }
        },
        colors: ["#845adf"],
        grid: {
            padding: {
                top: -10
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                shadeIntensity: 0.4,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 53, 91]
            },
        },
        labels: ['Average Results'],
    };
    var chartCle = new ApexCharts(document.querySelector("#activeUsersChart"), optionsCircl);
    chartCle.render();

    var chartCleIn = new ApexCharts(document.querySelector("#inactiveUsersChart"), optionsCircl);
    chartCleIn.render()


    var chartCleAm = new ApexCharts(document.querySelector("#ambassadorsC"), optionsCircl);
    chartCleAm.render()
</script>
