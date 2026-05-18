function toggleMenu() {
    var menu = document.getElementById("sidebarMenu");
    menu.classList.toggle("active");
}

function closeSidebar() {
    var menu = document.getElementById("sidebarMenu");
    if (menu) {
        menu.classList.remove("active");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    var sidebarLinks = document.querySelectorAll("#sidebarMenu a");
    sidebarLinks.forEach(function(link) {
        link.addEventListener("click", closeSidebar);
    });

    var menuToggle = document.getElementById("menuToggle");
    if (menuToggle) {
        menuToggle.addEventListener("click", function(event) {
            event.stopPropagation();
            toggleMenu();
        });
    }

    document.addEventListener("click", function(event) {
        var menu = document.getElementById("sidebarMenu");
        if (!menu) return;

        if (menu.classList.contains("active")) {
            var isClickInside = menu.contains(event.target) || (menuToggle && menuToggle.contains(event.target));
            if (!isClickInside) {
                closeSidebar();
            }
        }
    });
});

function liveSearch() {
    var query = document.getElementById("searchBox").value;
    var url = '../../Controller/php/searchController.php?query=' + query;

    fetch(url)
    .then(function(response) {
        return response.text();
    })
    .then(function(data) {
        document.getElementById("jobContainer").innerHTML = data;
    });
}

function applyFilter() {
    var categoryId = document.getElementById("filterCategory").value;
    var locationValue = document.getElementById("filterLocation").value;
    var jobTypeValue = document.getElementById("filterJobType").value;
    var salaryValue = document.getElementById("filterSalary").value;

    var url = '../../Controller/php/filterController.php?category_id=' + encodeURIComponent(categoryId) + 
              '&location=' + encodeURIComponent(locationValue) + 
              '&job_type=' + encodeURIComponent(jobTypeValue) + 
              '&salary=' + encodeURIComponent(salaryValue);

    fetch(url)
    .then(function(response) {
        return response.text();
    })
    .then(function(data) {
        document.getElementById("jobContainer").innerHTML = data;
    });
}