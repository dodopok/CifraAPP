var idCifra;
var isScrolling;
var scrolldelay; 
var velocidade = 100;
var userIsOnline = navigator.onLine;

$( document ).on( "pageinit", "#page", function() {

    favoritaAll();    

    if(!userIsOnline){
        $('#status').removeClass('online').addClass('offline').text('Você está offline');
    }

    $('#importCifra').submit(function (e) {

        $.mobile.loading( 'show', {
            theme: 'z',
            html: ""
        });

        //cache the form element for use in this function
        var $this = $(this);

        //prevent the default submission of the form
        e.preventDefault();
        if(userIsOnline){
            //run an AJAX post request to your server-side script, $this.serialize() is the data from your form being added to the request
            $.post($this.attr('action'), $this.serialize(), function (responseData) {
                $.mobile.loading('hide');
                $( "#popupImportCifra" ).popup( "close" );
                //in here you can analyze the output from your server-side script (responseData) and validate the user's login without leaving the page
            });
        }else{
            $( "#popupImportCifra" ).popup( "close" );
            alert('Aparentemente, você está no modo offline. \n Se conecte a internet para carregar novas cifras! \n\n Obrigado por usar o Cifra APP!');
        }
    });

    $(document).on("popupafterclose", "#popupImportCifra", function () {
        $( "#popupImport" ).popup( "open" );
        setTimeout(function() {
            $( "#popupImport" ).popup( "close" );
        }, 1500);
    });

    $( document ).on( "swipeleft swiperight", "#contentCA", function( e ) {
        // We check if there is no open panel on the page because otherwise
        // a swipe to close the left panel would also open the right panel (and v.v.).
        // We do this by checking the data that the framework stores on the page element (panel: open).
        if ( $.mobile.activePage.jqmData( "panel" ) !== "open" ) {
            if ( e.type === "swiperight"  ) {
                $( "#left-panel" ).panel( "open" );
            }
        }
    });
    
     $("#cifras-listview").listview({
        autodividers: true,
        autodividersSelector: function (li) {
            var out = li.attr("artist");
            return out;
        }
    }).listview("refresh");

    $( "#find-cifras" ).on( "listviewbeforefilter", function ( e, data ) {
        var $ul = $( this ),
            $input = $( data.input ),
            value = $input.val(),
            html = "";
        $ul.html( "" );
        if ( value && value.length > 2 ) {
            $ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>" );
            $ul.listview( "refresh" );
            $.ajax({
                url: "http://gd.geobytes.com/AutoCompleteCity",
                dataType: "jsonp",
                crossDomain: true,
                data: {
                    q: $input.val()
                }
            })
            .then( function ( response ) {
                $.each( response, function ( i, val ) {
                    html += "<li>" + val + "</li>";
                });
                $ul.html( html );
                $ul.listview( "refresh" );
                $ul.trigger( "updatelayout");
            });
        }
    });

    refreshSetlist();

}); 

function favoritaCifraAtual(){
    artist = $('li[data-id="'+idCifra+'"]').attr('artist');
    title = $('li[data-id="'+idCifra+'"]').text();
    chord = $("div[data-role='content']").html();
    favoritaCifra(idCifra, artist, title, chord);
    $( "#popupFavorite" ).popup( "open" );
    setTimeout(function() {
        $( "#popupFavorite" ).popup( "close" );
    }, 1500);
}

function favoritaCifra(idCifra, artist, title, chord){    
    cifra = {"artist": artist, "title" : title, "chord" : chord};
    localStorage.setItem(idCifra, JSON.stringify(cifra));
}

function favoritaAll(){
    $('#cifras-listview li').each(function(index){
        idCifra = $(this).attr('data-id');
        console.debug('Cacheando cifra '+idCifra);
        if (localStorage.getItem(idCifra) === null) {
            $.get('getCifra.php', {id: idCifra}, function (responseData) {
                cifra = {"artist": responseData.artist, "title" : responseData.title, "chord" : responseData.chord};
                localStorage.setItem(responseData.id, JSON.stringify(cifra));
            }, 'json');
        }
    });    
}

function loadCifra(id){
    var cifra;
    idCifra = id;
    $.mobile.loading( 'show', {
        theme: 'z',
        html: ""
    });
    if (localStorage.getItem(id)) {      
        cifra = JSON.parse(localStorage.getItem(id));
        showCifra(cifra);
    }else{
        if(userIsOnline){
            $.get('getCifra.php', {id: id}, function (responseData) {
                cifra = {artist: responseData.artist, title : responseData.title, chord : responseData.chord};
                showCifra(cifra);
            }, 'json');
        }else{
            alert('Aparentemente, você está no modo offline. \n Se conecte a internet para carreg novas cifras! \n\n Obrigado por usar o Cifra APP!');
        }
    }    
}

function showCifra(cifra){
    $("div[data-role='content']").html(cifra.chord);
    $("#title").text(cifra.artist+" - "+cifra.title);
    $.mobile.loading( 'hide');
    $("pre").transpose();
}

function transpose(){
    $("div[data-role='content'] span b").text().each(function(index, value){
        console.debug(value);
    });
}

function doScroll() {
    window.scrollBy(0,1);
    scrolldelay = setTimeout('doScroll()', velocidade);
    isScrolling = true;    
}

function scroll(){
    if(!isScrolling) {
        doScroll();
    }else{
        isScrolling = false;
        clearTimeout(scrolldelay);
    }
}

function addToSetlist(){
    name = $('li[data-id="'+idCifra+'"]').text();
    if(sessionStorage.getItem('setlist')){
        setlist = JSON.parse(sessionStorage.getItem('setlist'));
        setlist.push({id : idCifra, name : name});
    }else{        
        setlist = [{id : idCifra, name : name}];
    }
    sessionStorage.setItem('setlist', JSON.stringify(setlist));
    refreshSetlist();
}

function refreshSetlist(){
    $('#cifras-setlist').html('<li data-icon="plus" data-theme="b"><a href="#" onclick="addToSetlist();">Adicionar Cifra Atual à Setlist</a></li>');
    if(sessionStorage.getItem('setlist')){
        setlist = JSON.parse(sessionStorage.getItem('setlist'));        
        $(setlist).each(function(){            
            $('#cifras-setlist').append('<li><a href="#" data-id="'+this.id+'" onclick="loadCifra('+this.id+');">'+this.name+'</a></li>');            
        });
    }
    $('#cifras-setlist').listview('refresh');
}

function aumentaFonte(){
    tamanho = $('#chordT').css('font-size');
    $('#chordT').css('font-size', (parseInt(tamanho)+1)+'px');
}

function diminuiFonte(){
    tamanho = $('#chordT').css('font-size');
    $('#chordT').css('font-size', (parseInt(tamanho)-1)+'px');
}