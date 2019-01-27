    $(document).ready(function (){

        $("form.comments").submit(function (e){
            e.preventDefault();

            var form = $('.comments input[name=form]').val();
            var id = $('.comments input[name=url_parent]').val();
            var gu = $('.comments input[name=get_url]').val();
            var name = $('.comments input[name=name]').val();
            var email = $('.comments input[name=email]').val();
            var mes = $('.comments textarea[name=message]').val();
            var su = $('.playeriframe iframe').attr("data-video");
            $('.comments button[type="submit"]').attr('disabled', 'disabled');

            $.ajax({
                url: '/lib/processing.php',
                dataType: "html",
                cache: false,
                type: "POST",
                data: {id: id, gu: gu, form: form, name: name, email: email, message: mes, su: su},
                success: function (response){
                    $("#comments-load_" + form).html(response).fadeIn(500).fadeOut(8000);
                    $('.comments button[type="submit"]').prop('disabled', false);
                }
            });
            return false;
        });


        $("form.emails").submit(function (e){
            e.preventDefault();

            var form = $('.emails input[name=form]').val();
            var name = $('.emails input[name=name]').val();
            var email = $('.emails input[name=email]').val();
            var mes = $('.emails textarea[name=message]').val();
            $('.emails button[type="submit"]').attr('disabled', 'disabled');

            $.ajax({
                url: '/lib/processing.php',
                dataType: "html",
                cache: false,
                type: "POST",
                data: {form: form, name: name, email: email, message: mes},
                success: function (response){
                    $("#comments-load_" + form).html(response).fadeIn(1000).fadeOut(10000);
                    $('.emails button[type="submit"]').prop('disabled', false);
                }
            });
            return false;
        });

        $("form.contacts").submit(function (e){
            e.preventDefault();

            var form = $('.contacts input[name=form]').val();
            var name = $('.contacts input[name=name]').val();
            var email = $('.contacts input[name=email]').val();
            var subj = $('.contacts input[name=subject]').val();
            var mes = $('.contacts textarea[name=message]').val();
            $('.contacts button[type="submit"]').attr('disabled', 'disabled');

            $.ajax({
                url: '/lib/processing.php',
                dataType: "html",
                cache: false,
                type: "POST",
                data: {form: form, name: name, email: email, subject: subj, message: mes},
                success: function (response){
                    $("#comments-load_" + form).html(response).fadeIn(1000).fadeOut(10000);
                    $('.contacts button[type="submit"]').prop('disabled', false);
                }
            });
            return false;
        });
    });