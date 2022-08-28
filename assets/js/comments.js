;(function (factory) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', factory);
    } else {
        factory();
    }
}(function () {
    const oc = window.oc;

    class BlogHubComments {

        /**
         * Create new BlogHub Comments instance
         */
        constructor() {
            Array.from(document.querySelectorAll('[data-bloghub-handler]')).map(el => {
                el.addEventListener('click', (event) => {
                    let method = el.dataset.bloghubHandler;

                    if (typeof this[method] !== 'undefined') {
                        event.preventDefault();
                        this[method](el);
                    }
                });
            });
        }

        /**
         * Create a new Alert message box
         * @param {string} type 
         * @param {string|HTMLElement} content 
         * @returns 
         */
        createAlert(type, content) {
            let alert = document.createElement('DIV');
            alert.className = `alert alert-${type}`;

            if (content instanceof HTMLElement) {
                alert.appendChild(content);
            } else {
                alert.innerHTML = content;
            }

            return alert;
        }

        /**
         * Reload GREGWAR Captcha Image
         * @param {HTMLElement} el 
         */
        onReloadCaptcha(el) {
            let temp = el.innerHTML;
            el.disabled = true;
            el.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

            let submit = document.getElementById(`${el.dataset.bloghubId}_submit`);
            if (submit) {
                submit.disabled = true;
            }

            oc.ajax('onReloadCaptcha', {
                success: function(data, responseCode, xhr) {
                    let image = document.getElementById(`${el.dataset.bloghubId}_captchaImage`);
                    if (image) {
                        image.src = data.captchaImage;
                    }

                    el.innerHTML = temp;
                    el.disabled = false;
                    if (submit) {
                        submit.disabled = false;
                    }
                }
            })
        }

        /**
         * Like Comment
         * @param {HTMLElement} el 
         */
        onLike(el) {
            oc.ajax('onLike', {
                data: {
                    'form_id': el.dataset.bloghubId,
                    'comment_id': el.dataset.bloghubCommentId
                },
                error: function(data, responseCode, xhr) {
                    alert(data);
                }
            });
        }

        /**
         * Dislike Comment
         * @param {HTMLElement} el 
         */
        onDislike(el) {
            oc.ajax('onDislike', {
                data: {
                    'form_id': el.dataset.bloghubId,
                    'comment_id': el.dataset.bloghubCommentId
                },
                error: function(data, responseCode, xhr) {
                    alert(data);
                }
            });
        }

        /**
         * Favorite Comment
         * @param {HTMLElement} el 
         */
        onFavorite(el) {
            oc.ajax('onFavorite', {
                data: {
                    'form_id': el.dataset.bloghubId,
                    'comment_id': el.dataset.bloghubCommentId
                },
                error: function(data, responseCode, xhr) {
                    alert(data);
                }
            });
        }

        /**
         * Reply To Comment
         * @param {HTMLElement} el 
         */
        onReply(el) {
            oc.ajax('onReply', {
                data: {
                    'form_id': el.dataset.bloghubId,
                    'comment_id': el.dataset.bloghubCommentId
                },
                success: function(data, responseCode, xhr) {
                    let cancel = document.getElementById(`${el.dataset.bloghubId}_cancel`);
                    if (cancel) {
                        cancel.style.removeProperty('display');
                    }

                    let form = document.getElementById(el.dataset.bloghubId + '_form');
                    if (form) {
                        form.scrollIntoView({ behavior: "smooth" });
                        form.querySelector('button[type="submit"]').innerText = data.submitButtonText;

                        let hidden = document.createElement('INPUT');
                        hidden.type = 'hidden';
                        hidden.name = 'comment_parent';
                        hidden.value = data.comment.id;
                        form.appendChild(hidden);

                        this.success(data, responseCode, xhr);
                    }
                },
                error: function(data, responseCode, xhr) {
                    alert(data);
                }
            });
        }

        /**
         * Cancel Reply-To Comment
         * @param {HTMLElement} el 
         */
        onCancelReply(el) {
            oc.ajax('onCancelReply', {
                data: {
                    'form_id': el.dataset.bloghubId,
                },
                success: function(data, responseCode, xhr) {
                    let cancel = document.getElementById(`${el.dataset.bloghubId}_cancel`);
                    if (cancel) {
                        cancel.style.display = 'none';
                    }

                    let form = document.getElementById(el.dataset.bloghubId + '_form');
                    if (form) {
                        form.querySelector('button[type="submit"]').innerText = data.submitButtonText;

                        let hidden = form.querySelector('[name="comment_parent"]');
                        if (hidden) {
                            hidden.remove();
                        }

                        this.success(data, responseCode, xhr);
                    }
                }
            });
        }

        /**
         * Submit Comment or Reply
         * @param {HTMLElement} el 
         */
        onSubmit(el) {

        }

    }
    new BlogHubComments();

}));