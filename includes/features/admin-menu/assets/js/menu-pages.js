let _ae_$=jQuery;
_ae_$(document).ready(function () {
    //Wizard
    let openTab=function ($href){
        let target=$href.attr('aria-controls');
        _ae_$('.tab-pane.active').removeClass('active');
        _ae_$(".tab-pane#"+target).addClass('active');
    }
    _ae_$('a[data-toggle="tab"]').on('click', function (e) {
       e.preventDefault();
        openTab(_ae_$(this));
    });
    _ae_$('li.wizard-step[role="presentation"]').on('click', function (e) {
        openTab(_ae_$(this).find('a[data-toggle="tab"]'));
    });
});

_ae_$('.nav-tabs').on('click', 'li', function() {
    _ae_$('.nav-tabs li.active').removeClass('active');
    _ae_$(this).addClass('active');
});