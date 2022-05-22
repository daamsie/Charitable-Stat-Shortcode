/*
* This javascript makes it so that any tables created with Charitable Stat Shortcode are sortable.
*/

const charitableGetCellValue = (tr, idx) => tr.children[idx].dataset.amount || tr.children[idx].innerText || tr.children[idx].textContent;

const charitableTableSortComparer = (idx, asc) => (a, b) => ((v1, v2) => 
    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(charitableGetCellValue(asc ? a : b, idx), charitableGetCellValue(asc ? b : a, idx));

// do the work...
document.querySelectorAll('table.charitable-stat-shortcode-table th').forEach(th => th.addEventListener('click', (() => {
    const table = th.closest('table');
    Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
        .sort(charitableTableSortComparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
        .forEach(tr => table.appendChild(tr) );
})));