function toggleMenu() {
    var menu = document.getElementById("sidebarMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

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

    var url = '../../Controller/php/filterController.php?category_id=' + categoryId + 
              '&location=' + locationValue + 
              '&job_type=' + jobTypeValue + 
              '&salary=' + salaryValue;

    fetch(url)
    .then(function(response) {
        return response.text();
    })
    .then(function(data) {
        document.getElementById("jobContainer").innerHTML = data;
    });
}