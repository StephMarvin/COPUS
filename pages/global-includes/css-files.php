<!-- Google Fonts -->
<link href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,650,650i|Nunito:300,300i,400,400i,600,600i,650,650i|Poppins:300,300i,400,400i,500,500i,600,600i,650,650i" rel="stylesheet">

<!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>

<!-- FullCalendar Bootstrap 5  -->
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.20/index.global.min.js"></script>

<!-- Vendor CSS Files -->
<link href="../../public/main/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../../public/main/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="../../public/main/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="../../public/main/assets/vendor/quill/quill.snow.css" rel="stylesheet">
<link href="../../public/main/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
<link href="../../public/main/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
<link href="../../public/main/assets/vendor/simple-datatables/style.css" rel="stylesheet">

<!-- Template Main CSS File -->
<link href="../../public/main/assets/css/style.css" rel="stylesheet">
<link href="../../public/main/assets/css/custom-styles.css" rel="stylesheet">
<!--<link href="../../../public/main/assets/css/admin-details.css" rel="stylesheet">-->
<link href="../../public/main/assets/css/custom-table.css" rel="stylesheet">
<link href="../../public/main/assets/css/academics-style.css" rel="stylesheet">

<style>
    /* Smaller calendar text */
    #calendarDashboard {
        font-size: 0.80rem;
    }
    
    /* Center event text */
    #calendarDashboard .fc-event, .fc-event-title {
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        padding: 5px;
    }
    
    /* Adjust "+X more" link position */
    #calendarDashboard .fc-daygrid-more-link {
        display: block;
        margin-bottom: 2px;       /* space between last event and link */
        font-size: 0.65rem;
        text-align: center;
        cursor: pointer;
    }
    
    /* Optional: add hover effect */
    #calendarDashboard .fc-daygrid-more-link:hover {
        text-decoration: underline;
    }
    
    /* Day numbers smaller */
    #calendarDashboard .fc-daygrid-day-number {
        font-size: 0.65rem;
    }
    
    /* Toolbar text smaller */
    #calendarDashboard .fc-toolbar-title {
        font-size: 0.90rem;
        font-weight: bold;
    
    }
    
    /* Toolbar text smaller */
    #calendarDashboard .fc-toolbar button {
        font-size: 0.80rem;
        background-color: #E04C0B;
        border: none;
    }
    
    #calendarDashboard .fc-toolbar button:active,
    #calendarDashboard .fc-toolbar button:focus {
        filter: brightness(150%);
    }
    
    /* For Users */
    
    #calendar {
        font-size: 1rem;
    }
    
    /* Center event text */
    #calendar .fc-event, .fc-event-title {
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        border: none;
        padding: 5px;
        margin-bottom: 3px;
    }
    
    #calendar .fc-event-title:hover {
        filter: brightness(90%);
    }
    
    /* Adjust "+X more" link position */
    #calendar .fc-daygrid-more-link {
        display: inline-block;
        width: auto;
        padding: 10px 12px;
        margin-top: 2px;
        font-size: 0.85rem;         /* slightly larger for readability */
        font-weight: bold;
        color: #fff;                /* text color */
        background-color: #E04C0B;  /* background color */
        border-radius: 8px;         /* rounded corners */
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    /* Optional: add hover effect */
    #calendar .fc-daygrid-more-link:hover {
        text-decoration: underline;
    }
    
    /* Day numbers smaller */
    #calendar .fc-daygrid-day-number {
        font-size: 1rem;
    }
    
    /* Toolbar text smaller */
    #calendar .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    /* Toolbar text smaller */
    #calendar .fc-toolbar button {
        font-size: 1rem;
        background-color: #E04C0B;
        border: none;
    }
    
    #calendar .fc-toolbar button:active,
    #calendar .fc-toolbar button:focus {
        filter: brightness(150%);
    }
</style>