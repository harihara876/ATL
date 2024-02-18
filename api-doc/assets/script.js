let tables = document.getElementsByTagName("TABLE");

for (let i = 0; i < tables.length; i++) {
    tables[i].setAttribute("class", "table table-sm table-borderless");
}