// Function to make the HTTP API call and parse JSON data
async function fetchAndParseData(url) {
    try {
        // Make the API call using fetch
        const response = await fetch(url);

        // Check if the response is OK (status code 200-299)
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Parse the JSON data from the response
        const data = await response.json();

        // Log the data or use it as needed
        console.log(data);

        // Return the data for further use
        return data;
    } catch (error) {
        // Handle any errors that occurred during the fetch or parsing
        console.error('Error fetching or parsing data:', error);
    }
}

function unixTimestampToTimeString(timestamp, onlyTime=false){
    const date = new Date(timestamp);
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    let dateStr = ''
    if(!onlyTime)
    {
    let day =   (date.getDate())
    let month = (date.getMonth())
    let year =  (date.getFullYear())

    let today = new Date(Date.now())
    if(today.getFullYear() === year)
    {
        if(today.getMonth() === month)
        {
        if(today.getDate() === day)
        {
            // dateStr += 'Dnes '
        }
        else
        {
            dateStr += `${day}.${month+1}. `
        }
        }
        else
        {
        dateStr += `${day}.${month+1}. `
        }
    }
    else
    {
        dateStr += `${day}.${month+1}. ${year} `
    }

    }
    return `${dateStr}${hours}:${minutes}:${seconds}`;
};


// Example usage with async/await
async function printPlot(flexContainer, insertBeforeElemId, moduleId, moduleType, dataType) 
{
    // fetch data    
    const apiEndpointBase = 'https://akvaphp.charvot.cz/api/data/view';
    const apiData = await fetchAndParseData(`${apiEndpointBase}?moduleId=${moduleId}&dataType=${dataType}`)
    // prepare DOM
    const chartDiv = document.createElement('div')
    chartDiv.className = 'chart-container'
    const canvas = document.createElement('canvas');
    chartDiv.appendChild(canvas)
    flexContainer.insertBefore(chartDiv,document.getElementById(insertBeforeElemId))

    // prepare chart params
    let measQuantity = 'Neznámá veličina'// veličina
    let unit = '-'
    let xDataProcess = (xVal) => {
        const dateObj = new Date(xVal)
        return dateObj.getTime()-dateObj.getTimezoneOffset()*60000 // convert from UTC to local
      }
    let yDataProcess = (yVal) => {
        return yVal
      }

    let xTickProcess = (xTick) => `${unixTimestampToTimeString(xTick)}`
    let yTickProcess = (yTick) => yTick 


    switch(moduleType)
    {
        case 1:
            break;
        case 2:
            break;
        case 3:
            measQuantity = 'Teplota vody'// veličina
            unit = '˚C'
            break;
        case 4:
            if(dataType === 0)
            {
                measQuantity = 'Hladina vody'// veličina
                unit = '% snímače'
            }
            if(dataType === 1)
            {
                measQuantity = 'Hladina vody - plovák'// veličina
                yTickProcess = (yVal) => {
                    return (yVal == 1)?'Sepnuto':(yVal == 0)?'Rozepnuto':''
                  }
            }
            break;
        case 5:
            break;
    }
    

    const data = {
        datasets: [{
            label: `${measQuantity} [${unit}]`,
            data: apiData.map(row => ({
            x: xDataProcess(row.x),
            y: yDataProcess(row.y)
            })),
            // tension: 0.4 // cubic interpolation
            // borderWidth: 1,
            // pointRadius: 0,
        }]
    }

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        showLine: true,
        scales: {
            x: {
                ticks: {
                    callback: value => xTickProcess(value),
                    font: {size: 20}
                }
            },
            y: {
                ticks: {
                    callback: value => yTickProcess(value),
                    font: {size: 20}
                }
            },
        },
        plugins:{
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = '';
                        if (context.parsed.x !== null && context.parsed.y !== null)
                            label += `${unixTimestampToTimeString(context.parsed.x)}, ${measQuantity}: ${context.parsed.y} ${unit}`
                        return label;
                    }
                }
            },
            legend: {
                    labels: {
                        font: {size: 20}
                    }
            }
        },
        }


    // draw chart
    new Chart(canvas, {
        type: 'scatter',
        data: data,
        options: options
    });
}

