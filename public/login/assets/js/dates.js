document.addEventListener("DOMContentLoaded", function() {
    const yearDropdown = document.getElementById("year-dropdown");
    const monthDropdown = document.getElementById("month-dropdown");
    const dayDropdown = document.getElementById("day-dropdown");

    const addYearDropdown = () => {
        const getYear = Number(new Date().getFullYear());
        
        for(let i = getYear; i >= 1950; i--) {
            const year = document.createElement("option");
            year.value = i;
            year.textContent = i;

            yearDropdown.append(year);
        }
    }

    const addMonthDropdown = () => {
        const months = [
            "January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"
        ];

        for(let i = 0; i < months.length; i++) {
            const month = document.createElement("option");
            month.value = i + 1;
            month.textContent = months[i];

            monthDropdown.append(month);
        }
    }

    const addDayDropdown = () => {
        for(let i = 1; i <= 31; i++){
            const day = document.createElement("option");
            day.value = i;
            day.textContent = i;

            dayDropdown.append(day);
        }
    }

    addYearDropdown();
    addMonthDropdown(); 
    addDayDropdown();
})