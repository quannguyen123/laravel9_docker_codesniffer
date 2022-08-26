import $ from 'jquery'
import {Modal} from 'bootstrap';
import axios from 'axios';

const NAME = 'button'
const DATA_KEY = 'dym.button'
const EVENT_KEY = `.${DATA_KEY}`
const JQUERY_NO_CONFLICT = $.fn[NAME]
const EVENT_LOAD_DATA_API = `load${EVENT_KEY}`

const SELECTOR_CONFIRM_DELETE = '[data-action="confirm-delete"]'
const SELECTOR_CONFIRMED_DELETE = '[data-action="confirmed-delete"]'
const SELECTOR_SHOW_MODAL_CONFIRM = '[data-action="show-confirm-modal"]'
const SELECTOR_CONFIRMED_CHANGE_STATUS_USER = '[data-callback-action="change-status"]'
const SELECTOR_BTN_POPULAR = '[data-action="btn-popular"]'
const Default = {}

class Button {

    constructor(_element, _options) {
        this.element = _element
        this.options = $.extend({}, Default, _options)
    }

    template(template, data) {
        return new EJS({url: '/admin/tpl/' + template, cache: false, debug: true}).render(data);
    }

    init() {
        //...
    }

    show_confirm_delete() {
        let r = $(this.element);
        if ($('#confirm_delete').length) {
            $('#confirm_delete').remove();
        }
        $('body').append(this.template('confirm_delete', {
            id: r.data('id'), url: r.data('url'), title: r.data('title'), content: r.data('content'),
        }));

        $('#confirm_delete').modal('show');
    }

    confirmed_delete() {
        let id = this.element.data('id');
        axios.delete(this.element.data('url'), {
            responseType: 'json'
        }).then(function (response) {
            if (response.data.status) {
                $('#record-' + id).remove();
            }
        }).catch(function (error) {
            console.info(error);
        });
        $('#confirm_delete,.modal-backdrop').remove();
    }

    popular() {
        let img = this.element;
        axios.put(this.element.data('url'), {
            responseType: 'json',
        }).then(function (response) {
            let f = response.data.status ? 'active' : 'unactive';
            let i = '/admin/img/icons/common/start-' + f + '.svg';
            img.attr('src',i);
        }).catch(function (error) {
            console.trace(error);
        });
    }

    show_modal_confirm() {
        alert(123);

        // let r = $(this.element);
        // if ($('#modal_confirm').length) {
        //     $('#modal_confirm').remove();
        // }
        // $('body').append(this.template('modal_confirm', {
        //     id: r.data('id'), url: r.data('url'), title: r.data('title'), content: r.data('content'), callback: r.data('callback'), method: r.data('method'), no: r.data('button-no'), yes: r.data('button-yes')
        // }));

        // $('#modal_confirm').modal('show');
    }

    confirmed_change_status_user() {
        let id = this.element.data('id');
        let method = this.element.data('method');
        let url = this.element.data('url');

        axios({
            method: method,
            url: url,
            responseType: 'json'
        }).then(function (response) {
            if (response.status == 200) {
                if (response.data.user.status == 1) {
                    $('#record-' + id).find('.user-in-active').addClass('display-none');
                    $('#record-' + id).find('.user-active').removeClass('display-none');
                } else {
                    $('#record-' + id).find('.user-in-active').removeClass('display-none');
                    $('#record-' + id).find('.user-active').addClass('display-none');
                }
                $("#alert-response-ajax")
                    .removeClass('alert-success')
                    .removeClass('alert-danger')
                    .removeClass('show')
                    .removeClass('p-0')
                    .removeClass('m-0')
                    .addClass('alert-success')
                    .addClass('show');
                $("#alert-response-ajax .message").text(response.data.message || 'Success')
            } else {
                $("#alert-response-ajax")
                    .removeClass('alert-success')
                    .removeClass('alert-danger')
                    .removeClass('show')
                    .removeClass('p-0')
                    .removeClass('m-0')
                    .addClass('alert-danger')
                    .addClass('show');
                $("#alert-response-ajax .message").text(response.data.message || 'Fail');
            }
        }).catch(function (error) {
            console.info(error);
        });
        $('#modal_confirm,.modal-backdrop').remove();
    }

    static _jQueryInterface(config, e) {
        let data = $(this).data(DATA_KEY)
        const _options = $.extend({}, Default, $(this).data())

        if (!data) {
            data = new Button($(this), _options)
            $(this).data(DATA_KEY, typeof config === 'string' ? data : config)
        }

        if (config !== undefined || typeof config === 'string') {
            data[config](e)
        } else {
            data.init($(this))
        }
    }
}

$(document)
    .on('click', SELECTOR_CONFIRM_DELETE, function (event) {
        Button._jQueryInterface.call($(this), 'show_confirm_delete', event)
    })
    .on('click', SELECTOR_CONFIRMED_DELETE, function (event) {
        Button._jQueryInterface.call($(this), 'confirmed_delete', event)
    })
    .on('click', SELECTOR_SHOW_MODAL_CONFIRM, function (event) {
        Button._jQueryInterface.call($(this), 'show_modal_confirm', event)
    })
    .on('click', SELECTOR_CONFIRMED_CHANGE_STATUS_USER, function (event) {
        Button._jQueryInterface.call($(this), 'confirmed_change_status_user', event)
    })
    .on('click', SELECTOR_BTN_POPULAR, function (event) {
        Button._jQueryInterface.call($(this), 'popular', event)
    });

$.fn[NAME] = Button._jQueryInterface
$.fn[NAME].Constructor = Button
$.fn[NAME].noConflict = function () {
    $.fn[NAME] = JQUERY_NO_CONFLICT
    return Button._jQueryInterface
}

export default Button
