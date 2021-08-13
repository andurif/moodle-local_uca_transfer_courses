$(document).ready(
    function ($) {
        $("#id_category").hide();
        $("#tree_div").hide();
        var selected_cat_html = $("#tree_div").attr('data-select-label')+"<span id='selected_category' style='font-weight: bold'>"+$("#tree_div").attr('data-default-name')+"</span>";
        $("#id_category").parents(".felement").html(selected_cat_html+$("#tree_div").html()+$("#id_category").parents(".felement").html());

        //Action lancée au moment du click sur une catégorie
        $('.course_category_tree').on('click', '.content a',function(e) {
            var link = $(this);
            link.parent().click();
            $(".bold").each(function() {
                $(this).removeClass('bold');
            });
            link.addClass('bold');
            if (link.html().indexOf(' année') >= 0 || link.html().indexOf('Année unique') >= 0) {
                //Si on cliqué au niveau d'une année, on affichera le nom du diplôme à la suite pour plus de lisibilité
                var cat_text = link.html() + ", " + link.parents('.category.with_children.loaded').first().children('.info').children('.categoryname').children('a').html();
            } else {
                var cat_text = link.html();
            }
            $("#selected_category").text(cat_text);

            // On récupère l'id de la categorie pour le mettre dans le champ caché du formulaire.
            var catid = link.attr('href').split('=')[1];
            $("#id_category").val(catid);
            return false;
        });

        $("#select_default_category").click(function(e) {
            e.preventDefault();
            $(".bold").each(function(){
                $(this).removeClass('bold');
            });
            var default_name = $(this).attr('data-default-name');
            $("#id_category").val($(this).attr('data-default-id'));
            $("#selected_category").text(default_name);
        });
    }
);
