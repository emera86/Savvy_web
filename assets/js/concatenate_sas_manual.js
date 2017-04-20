var fragment = create('<div>Hello!</div><p>...</p>');

function create() {
    var sasheader = "../../sas_manual_header.html" ;
    var sascontent = "../../sas_manual_content.html" ;
    var sasending = "../../sas_manual_ending.html"
    var sasmanual = sasheader + sascontent + sasending ;
    return sasmanual;
}

var completepage = create();
