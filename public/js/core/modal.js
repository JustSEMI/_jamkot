/**
 * JAMKOT Custom Modal System
 * Menggantikan native browser confirm() dan alert()
 *
 * API:
 *   JKModal.confirm({ title, message, onConfirm, onCancel, type, confirmText, cancelText })
 *   JKModal.alert({ title, message, type, onOk, confirmText })
 */

(function () {
    'use strict';

    const _iconMap = {
        danger:  { icon: 'fa-solid fa-trash-can',             cls: 'danger' },
        warning: { icon: 'fa-solid fa-triangle-exclamation',  cls: 'warning' },
        info:    { icon: 'fa-solid fa-circle-info',            cls: 'info' },
        error:   { icon: 'fa-solid fa-circle-xmark',          cls: 'danger' },
        success: { icon: 'fa-solid fa-circle-check',          cls: 'info' },
    };

    function _overlay()   { return document.getElementById('jk-modal-overlay'); }
    function _el(id)      { return document.getElementById(id); }

    function _openModal() {
        _overlay().classList.add('visible');
    }

    function _closeModal() {
        const o = _overlay();
        if (o) o.classList.remove('visible');
    }

    // Close on backdrop click
    document.addEventListener('click', function (e) {
        const o = _overlay();
        if (o && e.target === o) _closeModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        const o = _overlay();
        if (e.key === 'Escape' && o && o.classList.contains('visible')) {
            _closeModal();
        }
    });

    function _cloneBtn(id) {
        const btn = _el(id);
        const clone = btn.cloneNode(true);
        btn.parentNode.replaceChild(clone, btn);
        return clone;
    }

    window.JKModal = window.JKModal || {};

    /**
     * Confirmation dialog (two buttons: cancel + confirm)
     */
    JKModal.confirm = function (opts) {
        const type = opts.type || 'danger';
        const conf = _iconMap[type] || _iconMap.danger;

        _el('jk-modal-icon').className   = 'jk-modal-icon ' + conf.cls;
        _el('jk-modal-icon-i').className = conf.icon;
        _el('jk-modal-title').textContent   = opts.title   || 'Konfirmasi';
        _el('jk-modal-message').textContent = opts.message || 'Apakah Anda yakin?';

        const cancelBtn  = _cloneBtn('jk-modal-btn-cancel');
        const confirmBtn = _cloneBtn('jk-modal-btn-confirm');

        cancelBtn.textContent = opts.cancelText || 'Batal';
        cancelBtn.style.display = '';
        cancelBtn.className = 'jk-modal-btn jk-modal-btn-cancel';

        confirmBtn.textContent = opts.confirmText || 'Ya, Lanjutkan';
        confirmBtn.className   = 'jk-modal-btn jk-modal-btn-confirm';

        cancelBtn.addEventListener('click', function () {
            _closeModal();
            if (typeof opts.onCancel === 'function') opts.onCancel();
        });

        confirmBtn.addEventListener('click', function () {
            _closeModal();
            if (typeof opts.onConfirm === 'function') opts.onConfirm();
        });

        _openModal();
    };

    /**
     * Alert dialog (single OK button)
     */
    JKModal.alert = function (opts) {
        const type = opts.type || 'warning';
        const conf = _iconMap[type] || _iconMap.warning;

        _el('jk-modal-icon').className   = 'jk-modal-icon ' + conf.cls;
        _el('jk-modal-icon-i').className = conf.icon;
        _el('jk-modal-title').textContent   = opts.title   || 'Perhatian';
        _el('jk-modal-message').textContent = opts.message || '';

        const cancelBtn  = _cloneBtn('jk-modal-btn-cancel');
        const confirmBtn = _cloneBtn('jk-modal-btn-confirm');

        // Hide cancel for alert
        cancelBtn.style.display = 'none';

        confirmBtn.textContent = opts.confirmText || 'OK';
        confirmBtn.className   = 'jk-modal-btn jk-modal-btn-confirm ok';

        confirmBtn.addEventListener('click', function () {
            _closeModal();
            if (typeof opts.onOk === 'function') opts.onOk();
        });

        _openModal();
    };
})();
