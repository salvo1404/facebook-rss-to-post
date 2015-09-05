(function ($) {

    $('document').ready(function () {

        // Edit-buttons
        $('body').on('click', 'a.toggle-edit', function () {
            $('#edit_' + $(this).attr('data-target')).toggleClass('show');
            $('#display_' + $(this).attr('data-target')).toggleClass('show');
            return false;
        });

        // Delete-buttons
        $('body').on('click', 'a.delete-row', function () {
            $('#edit_' + $(this).attr('data-target')).remove();
            $('#display_' + $(this).attr('data-target')).remove();
            update_ids();
            return false;
        });

        if ($("#fb_rss-feed-table").length) {

            $("#fb_rss-feed-table").on("fb-rss-changed", "tr", function () {
                var $tr = $(this),
                    id = $tr.attr("id").replace("display_", "").replace("edit_", ""),
                    $tr_data = $("#display_" + id),
                    $tr_edit = $("#edit_" + id),
                    fields = $tr_data.data("fields").split(",");
                $.each(fields, function (i) {
                    var field = ".field-" + fields[i];
                    $tr_data.find(field).text($tr_edit.find(field).val());
                });
                $tr_data.addClass("fb-rss-unsaved");
            });

            var do_save = false;
            $(window).bind('beforeunload', function () {
                if (!do_save && $("#fb_rss-feed-table .fb-rss-unsaved").length) {
                    return fb_rss.l18n.unsaved;
                }
            });
            $("#fb_rss-settings-form").on("submit", function () {
                do_save = true;
            });
            // Monitor dynamic inputs
            $("#fb_rss-feed-table").on('change', ':input', function () { //triggers change in all input fields including text type
                $(this).parents("tr.edit-row").trigger("fb-rss-changed");
            });

        }

        $('a.add-row').on('click', function (e) {
            e.preventDefault();
            var id = uniqid();
            $("#fb_rss-feed-table > tbody .empty_table").parent("tr").remove();
            $tr_data = $("#fb_rss-feed-table > tfoot > tr.data-row").clone().attr("id", "display_" + id).appendTo("#fb_rss-feed-table > tbody");
            $tr_edit = $("#fb_rss-feed-table > tfoot > tr.edit-row").clone().attr("id", "edit_" + id).appendTo("#fb_rss-feed-table > tbody");
            $tr_data.find(".toggle-edit,.delete-row").attr("data-target", id);
            $tr_edit.find(".toggle-edit").attr("data-target", id);
            $tr_edit.find("[name='id']").val(id);
            $tr_edit.find("[for^=0-]").each(function () {
                $(this).attr("for", $(this).attr("for").replace("0-", id + "-"));
            });
            $tr_edit.find("[id^=0-]").each(function () {
                $(this).attr("id", $(this).attr("id").replace("0-", id + "-"));
            });
            $tr_edit.find("[name^=0-]").each(function () {
                $(this).attr("name", $(this).attr("name").replace("0-", id + "-"));
            });
            update_ids();
            $("#" + id + "-name").focus().select();
        });

        $('#save_and_import').on('click', function () {
            $('#save_to_db').val('true');
        });

        if (Modernizr !== undefined && Modernizr.input.min && Modernizr.input.max)
            $("#fb_rss-settings-form [type='submit']").on("click", function (e) {
                $("[name$='-max_posts']").each(function () {
                    var max_posts = {
                        val: parseInt($(this).val()),
                        min: parseInt($(this).attr("min")),
                        max: parseInt($(this).attr("max")),
                        id: $(this).attr("id").replace("-max_posts", "")
                    }
                    if (max_posts.val < max_posts.min || max_posts.val > max_posts.max) {
                        $("#edit_" + max_posts.id).addClass("show");
                        $("#display_" + max_posts.id).addClass("show");
                    }
                });
            });

        $('body').delegate('a.show-main-ui', 'click', function () {
            $('#main_ui').show();
            $('.ajax_content').html('');
            return false;
        });

        $('body').delegate('a.clear-log', 'click', function () {
            $.ajax({
                type: 'POST',
                url: fb_rss.ajaxurl,
                data: ({
                    action: 'fb_rss_clear_log'
                }),
                success: function (data) {
                    $('.log').html(data);
                }
            });
            return false;
        });

        $("#from_date").datepicker();
        $("#till_date").datepicker();


        if ($("#fb_rss_progressbar").length && feeds !== undefined && feeds.count) {
            var import_feed = function (id) {
                $.ajax({
                    type: 'POST',
                    url: fb_rss.ajaxurl,
                    data: {
                        action: 'fb_rss_import',
                        feed: id
                    },
                    success: function (data) {
                        var data = data.data || {};
                        $("#fb_rss_progressbar").progressbar({
                            value: feeds.processed()
                        });
                        $("#fb_rss_progressbar_label .processed").text(feeds.processed());
                        if (data.count !== undefined) feeds.imported(data.count);
                        if (feeds.left()) {
                            $("#fb_rss_progressbar_label .count").text(feeds.imported());
                            import_feed(feeds.get());
                        } else {
                            $("#fb_rss_progressbar_label").html("Import completed. Imported posts: " + feeds.imported());
                        }
                    }
                });
            }
            $("#fb_rss_progressbar").progressbar({
                value: 0,
                max: feeds.total()
            });
            $("#fb_rss_progressbar_label").html("Import in progres. Processed feeds: <span class='processed'>0</span> of <span class='max'>" + feeds.total() + "</span>. Imported posts so far: <span class='count'>0</span>");
            import_feed(feeds.get());
        }

    });

})(jQuery);

function update_ids() {

    ids = jQuery("#fb_rss-feed-table > tbody input[name='id']").map(function () {
        return jQuery(this).val();
    }).get().join();

    jQuery('#ids').val(ids);

}

var feeds = {
    ids: feeds || [],
    count: feeds && feeds.length ? feeds.length : 0,
    imported_posts: 0,
    set: function (ids) {
        this.ids = ids;
        this.count = ids.length;
    },
    get: function () {
        return this.ids.splice(0, 1)[0];
    },
    left: function () {
        return this.ids.length;
    },
    processed: function () {
        return this.count - this.ids.length;
    },
    total: function () {
        return this.count;
    },
    imported: function (num) {
        if (num !== undefined && !isNaN(parseInt(num))) this.imported_posts += parseInt(num);
        return this.imported_posts;
    }
};
