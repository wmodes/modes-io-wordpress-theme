$(function(){
    setTimeout(function(){

        // Deal with Pano problems
        //
        // Because a long pano image (even hiddden) blows out the
        // right margin, we render this with "display: none;"
        // and then use jQuery to turn it on when the page
        // renders:
        
        $(".wp-block-image.pano figure,figure.pano")
            .css("display", "block");
        

        // Deal with fullwidth images - now dealt with through CSS
        // $(".wp-block-image.alignfull").css("margin-left", ($(".wp-block-image.alignfull").offset().left * -1) + "px");
        // $(window).resize(function(){
        //     $(".wp-block-image.alignfull").css("margin-left", ($(".wp-block-image.alignfull").offset().left * -1) + "px");
        // });

        // Give title a flair
        // we will change the following:
        //      ".archive .page-title"
        //      ".post .entry-title"
        //
        titleSelector = "body.home .page-title," + 
                        "body.archive .page-title," + 
                        "body.single .entry-title," + 
                        ".navbar-brand a," + 
                        "a.navbar-brand";
        $(titleSelector).each(function() {
            title = $(this).html();
            var midIndex = Math.floor(title.length / 2);
            // console.log("title:",title);
            // one word or more than one?
            if (title.match(/\W/gi) != null) {
                for (i=0; i<midIndex; i++) {
                    if (/\W/.test(title[midIndex + i])) {
                        var breakIndex = midIndex + i;
                        break;
                    }
                    if (/\W/.test(title[midIndex - i])) {
                        var breakIndex = midIndex - i;
                        break;
                    }
                }
                var firstPart = title.substring(0, breakIndex);
                var secondPart = title.substring(breakIndex, title.length);
            }
            else {
                var firstPart = title.substring(0, midIndex);
                var secondPart = title.substring(midIndex, title.length);
            }
            var newTitle = firstPart + "<span class=title-spice>" + secondPart + "</span>";
            $(this).html(newTitle)
        });

        // Make home page text fit
        if ($(".home .page-title").length == 1) {
            // Using http://fittextjs.com/
            $.getScript("/wp-content/themes/modes-io-understap/js/jquery.fittext.js", function() {
                jQuery(".page-title").fitText(0.99);
            });
        }

        // Move home page button into .post
        if ($(".home .postbox").length) {
            // for each postbox
            $(".postbox").each(function() {
                // find button
                // save and detatch button
                var catButton = $(this).children('.cat-link-btn').detach();
                // insert in proper place
                $(this).children('.post').append(catButton);
                catButton.css("display", "block");
            });
        }



    }, 250);
})