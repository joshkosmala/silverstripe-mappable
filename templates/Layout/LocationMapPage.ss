<div class="container bg-testimonials">
    <div class="container-fluid custom-font-family">
        <br/>
        <br/>
        <section id="map text-center">
            <h1>
                <black id="title">$Title</black>
            </h1>
            $Content
            <div class="content">
                <div class="row text-center">
                    <div class="col-lg-12 m-auto">
                        <select id="select-region" class="custom-select" onchange="filterByRegion(this);">
                            <% loop $getRegions %>
                                <option>$Value</option>
                            <% end_loop %>
                        </select>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-lg-12">
                        <br/>
                        or
                        <br/>
                        <br/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 m-auto pb-3 input-group mobile-search">
                        <input id="search" type="text" class="form-control" placeholder="Search...">
                        <span class="input-group-btn">
                        <a id="searchButton" href=''
                           onclick="this.href='stockists/?search='+document.getElementById('search').value"><button
                                class="btn btn-default" style=" cursor: pointer !important;" type="button">Search</button></a>

                        <a id="searchButton" href=''
                           onclick="this.href='stockists/'"><button class="btn ml-1 clear-button" type="button">Clear</button></a>
                    </span>
                    </div>
                </div>
                <br/>
                $FilterSummary
                <div class="row">
                    <div class="list-group col-lg-6"
                         style="padding-left: 15px; max-height: 650px; margin-bottom: 10px; overflow:scroll; -webkit-overflow-scrolling: touch;">
                        <% if $locationDataList %>
                            <% loop $locationDataList %>
                                <a onclick="this.href='stockists/?search='+document.getElementById('$Address').value+'&gridFilter=true'"
                                   class="list-group-item list-group-item-action flex-column align-items-start" style="cursor: pointer">

                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">$Name</h5>
                                        <input type="hidden" id="$Address" value="$Address" class="mb-1"/>
                                        <small class="text-muted">$City, $Region</small>
                                    </div>
                                    <p class="mb-1">$Address, $City, $Region</p>
                                    <small class="text-muted" href="tel:$PhoneNumber"><span
                                            class="fa fa-phone">&nbsp;</span>$PhoneNumber</small>
                                </a>
                            <% end_loop %>
                        <% else %>
                            <div class="text-center">
                                No stockists found
                            </div>
                        <% end_if %>

                    </div>
                    <div class="col-lg-6">
                        $Map
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    $("input[id='search']").focus();

    $(document).on('keypress', function (event) {
        if (event.which == 13) {
            window.location.href = "/stockists/?search=" + $("input[id='search']").val();
        }
    })

    function filterByRegion(sel) {
        window.location.href = "/stockists/?search=" + sel.value;
    }

    if (document.getElementById('searchinfo').textContent != '') {
        if (window.location.href.indexOf("gridFilter=true") > -1) {
            $('#select-region').val('Please select a region');
        } else if (document.getElementById('searchinfo').textContent.indexOf("Auckland") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Bay Of Plenty") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Canterbury") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Coromandel") > -1
        || document.getElementById('searchinfo').textContent.indexOf("East Cape") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Hawkes Bay") > -1
        || document.getElementById('searchinfo').textContent.indexOf("King Country") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Marlborough") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Nelson") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Northland") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Otago") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Southland") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Taranaki") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Taupo") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Waikato") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Wairarapa") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Wanganui") > -1
        || document.getElementById('searchinfo').textContent.indexOf("Wellington") > -1
        || document.getElementById('searchinfo').textContent.indexOf("West Coast") > -1
        ) {
            $('#select-region').val(document.getElementById('searchinfo').textContent);
        } else {
            $('#select-region').val('Please select a region');
        }
    }


</script>
