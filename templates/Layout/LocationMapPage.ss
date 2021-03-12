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
            <div class="col-lg-6 col-md-12 col-sm-12 m-auto pb-3 input-group mobile-search">
                <input id="search" type="text" class="form-control" placeholder="Search...">
                <span class="input-group-btn">

            <a id="searchButton" href=''
               onclick="this.href='stockists/?search='+document.getElementById('search').value"><button class="btn btn-default" style=" cursor: pointer !important;" type="button">Search</button></a>

            <a id="searchButton" href=''
                onclick="this.href='stockists/'"><button class="btn ml-1 clear-button"  type="button">Clear</button></a>
        </span>
            </div>
            <p>$Map</p>
            <br/>
        </div>
        </section>
    </div>
</div>

<script>
    $("input[id='search']").focus();

    $(document).on('keypress',function(event) {
        if(event.which == 13) {
            window.location.href = "/stockists/?search="+$("input[id='search']").val();
        }
    })
</script>
