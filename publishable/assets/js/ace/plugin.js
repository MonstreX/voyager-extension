/*
 * Ace Editor Plugin for TinyMCE 4.0
 * By Daniel Jones - info@d-k-j.com
 */

tinymce.PluginManager.add("ace", function(a) {

    function t() {
        console.log('Press CODE');

        var t = $(window).innerWidth() - 30,
            i = $(window).innerHeight() - 120;
        t > 1800 && (t = 1800), i > 1200 && (i = 1200);

        var x = (t - 20) % 138;

        if (t = t - x + 10, t > 600) {
            var x = (t - 20) % 138;
            t = t - x + 10
        }

        a.windowManager.open({
            title: "Code Editor",
			type: "container",
			id: "mce-ace-editor",
			name: "ace",
			width: t,
			height: i,
			inline: 1,
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'htmlpanel',
                        html: '<div id="mce-ace-editor-body"></div>'
                    }
                ]
            },
			buttons:[
				{
                    type: 'custom',
					text: "Ok",
					classes: "primary",
					onclick : function() {
                        console.log('Press OK');
						if(typeof mce_editor !== "undefined") {
							var html = mce_editor.getValue();

							a.focus(), a.undoManager.transact(function() {
								a.setContent(html)
							}), a.selection.setCursorLocation(), a.nodeChanged();

							a.windowManager.close();
						}
					}
				},
				{
                    type: 'custom',
					text: "Cancel",
					onclick: "close"
				}
			]
        });

        console.log($('#mce-ace-editor-body').length);

		if($('#mce-ace-editor-body').length) {



            ace.config.set('basePath', `/${rootAdminRoute}/voyager-assets/?path=js/ace/libs`);

            //voyager-assets/?path=js/skins/voyager/fonts/tinymce.eot

			$('#mce-ace-editor-body').append('<div id="mce-ace-editor-block"></div>');

			// Load editor
			$('#mce-ace-editor-block').css({'position':'absolute', 'left':'0', 'right':'0', 'top':'0', 'bottom':'0'});

			var mce_editor = ace.edit('mce-ace-editor-block');
			mce_editor.setTheme("ace/theme/monokai");
			mce_editor.getSession().setMode("ace/mode/html");

			mce_editor.setOptions({
				showPrintMargin: false
			});
			mce_editor.getSession().setUseWrapMode(true);

			// Set editor contents
			mce_editor.getSession().setValue(a.getContent({
				source_view: !0
			}));
		}

		// Only thing needed to get Ace to work is to remove the mce-container class
		// Tinymce re-adds the class so we need to loop it
		// TODO: Find a less HORRIFIC solution for this
		var countchecks = 0;

		function soddoffmce() {
			// Lets do it say 50 times (for 5 seconds)...
			if(countchecks++ == 50) {
				clearTimeout(soddoffloop);
				return;
			}

			soddoffloop = setTimeout(function () {
				if($('#mce-ace-editor').hasClass('mce-container')) $('#mce-ace-editor').removeClass('mce-container');
				soddoffmce();
			}, 100);
		}

		soddoffmce();
    }

    a.addCommand("mceace_codeEditor", t), a.ui.registry.addButton("ace", {
        icon: "code",
        tooltip: "Code Editor",
        onAction: t
    }), a.ui.registry.addMenuItem("ace", {
        icon: "code",
        text: "Code Editor",
        context: "tools",
        onAction: t
    })


    // a.addCommand("mceace_codeEditor", t), a.ui.registry.addButton("ace", {
    //     icon: "code",
    //     tooltip: "Code Editor",
    //     onclick: t
    // }), a.ui.registry.addMenuItem("ace", {
    //     icon: "code",
    //     text: "Code Editor",
    //     context: "tools",
    //     onclick: t
    // })

});
