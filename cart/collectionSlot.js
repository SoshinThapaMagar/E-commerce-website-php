const collectionSlots = [
    {
        minTime: 10,
        maxTime: 13,
    },
    {
        minTime: 13,
        maxTime: 16,
    },
    {
        minTime: 16,
        maxTime: 19,
    },
];

const collectionDays = [
    {
        name: 'Wed',
        value: 3,
    },
    {
        name: 'Thus',
        value: 4,
    },
    {
        name: 'Fri',
        value: 5,
    },
];

const currentDate = new Date();
const currentDay = currentDate.getDay();
const currentTime = currentDate.getHours();

const collectionDaySelect = document.querySelector('.collection-day');
const collectionTimeSelect = document.querySelector('.collection-time');

window.addEventListener('load', () => {
    // setting collection days
    collectionDays.forEach(collectionDay => {
        const dayOption = document.createElement('option');

        // if the collection time has exceeded or all of tomorrow's collection slots are more than 24 hours away
        if (
            currentDay >= collectionDay.value ||
            (collectionDay.value - currentDay == 1 &&
                currentTime > collectionSlots[2].maxTime)
        ) {
            dayOption.setAttribute('value', `Following ${collectionDay.name}`);
            dayOption.textContent = collectionDay.name + ' (Next week)';
            collectionDaySelect.appendChild(dayOption);
        } else {
            dayOption.setAttribute('value', collectionDay.name);
            dayOption.textContent = collectionDay.name;
            collectionDaySelect.appendChild(dayOption);
        }
    });

    // setting collection times
    setCollectionTimes();
});

collectionDaySelect.addEventListener('change', setCollectionTimes);

function setCollectionTimes() {
    collectionTimeSelect.innerHTML = '';
    let selectedDay;

    const selectedDayName =
        collectionDaySelect.value.length > 5
            ? collectionDaySelect.value.split(' ')[1]
            : collectionDaySelect.value;

    for (day of collectionDays) {
        if (day.name === selectedDayName) {
            selectedDay = day.value;
            break;
        }
    }

    if (
        selectedDay - currentDay === 1 &&
        currentTime < collectionSlots[2].maxTime
    ) {
        const defaultOption = document.createElement('option');
        defaultOption.disabled = true;
        defaultOption.selected = true;

        for (time of collectionSlots) {
            if (time.maxTime > currentTime) {
                const slotOption = document.createElement('option');

                slotOption.setAttribute(
                    'value',
                    `${time.minTime}-${time.maxTime}`
                );
                slotOption.textContent = `${time.minTime}-${time.maxTime}`;

                collectionTimeSelect.appendChild(slotOption);
            }
        }
    } else {
        for (time of collectionSlots) {
            const slotOption = document.createElement('option');

            slotOption.setAttribute('value', `${time.minTime}-${time.maxTime}`);
            slotOption.textContent = `${time.minTime}-${time.maxTime}`;

            collectionTimeSelect.appendChild(slotOption);
        }
    }
}
