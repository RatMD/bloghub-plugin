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
         * @param {HTMLElement} parent Parent Container
         */
        constructor(parent) {
            this.parent = parent;
            this.parent.addEventListener('click', this.listener.bind(this));
            this.parent.addEventListener('submit', this.listener.bind(this));
        }

        /**
         * EventListener
         * @param {Event} event 
         */
        listener(event) {
            let target = event.target.closest('[data-bloghub-handler]');
            if (!target) {
                return;
            }

            // Skip Submit Event on Form
            if (target.tagName.toUpperCase() === 'FORM' && event.type !== 'submit') {
                return;
            }

            // Call Method
            let method = target.dataset.bloghubHandler;
            if (typeof this[method] !== 'undefined') {
                event.preventDefault();
                if (target.classList.contains('disabled') || target.disabled) {
                    return;
                } else {
                    this[method](target);
                }
            }
        }

        /**
         * Turn string into HTML element
         * @param {string} content 
         * @returns {HTMLElement}
         */
        stringToElement(content) {
            let temp = document.createElement('DIV');
            temp.innerHTML = content;
            return temp.children[0];
        }

        /**
         * Create a new Alert message box
         * @param {string} type 
         * @param {string|HTMLElement} content 
         * @returns {HTMLElement}
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
         * Show Loading Indicator
         * @param {NodeList} elements 
         */
        showLoading(elements) {
            Array.from(elements).map(el => {
                if (el.tagName.toUpperCase() === 'button') {
                    el.disabled = true;
                } else {
                    el.classList.add('disabled');
                }
                el.dataset.bloghubContent = el.innerHTML;
                el.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                el.blur();
            });
        }

        /**
         * Hide Loading Indicator
         * @param {NodeList} elements 
         */
        hideLoading(elements) {
            Array.from(elements).map(el => {
                if (el.tagName.toUpperCase() === 'button') {
                    el.disabled = false;
                } else {
                    el.classList.remove('disabled');
                }
                el.innerHTML = el.dataset.bloghubContent;
                delete el.dataset.bloghubContent;
            });
        }

        /**
         * Call October AJAX handler
         * @param {string} method The desired ajax method to call.
         * @param {object} data The data to send.
         * @param {object} config Additional configuration for the oc.ajax method,
         * @returns Promise
         */
        callOctober(method, data, config) {
            return new Promise((resolve, reject) => {
                oc.ajax(method, Object.assign({
                    data,
                    success: function (data, responseCode, xhr) {
                        resolve({ data, responseCode, xhr, oc: this });
                    },
                    error: function (data, responseCode, xhr) {
                        reject({ data, responseCode, xhr, oc: this });
                    }
                }, typeof config === 'object' ? config : {}));
            });
        }

        /**
         * Change Comment Status (Approve, Reject, Favorite)
         * @param {HTMLElement} el 
         */
        onChangeStatus(el) {
            if (!el.dataset.bloghubStatus || !el.dataset.bloghubId) {
                return false;
            }
            let parent = el.closest('[data-comment-id]');

            // Show Loading Indicator
            if (parent) {
                this.showLoading(parent.querySelectorAll('[data-bloghub-handler="onChangeStatus"]'));
            }
            
            // Call AJAX backend
            this.callOctober('onChangeStatus', { 
                status: el.dataset.bloghubStatus,
                comment_id: el.dataset.bloghubId
            }).then(
                ({ data, responseCode, xhr, oc }) => {
                    if (parent) {
                        this.hideLoading(parent.querySelectorAll('[data-bloghub-handler="onChangeStatus"]'));
                        
                        if (data.comment) {
                            parent.replaceWith(this.stringToElement(data.comment));
                        } else {
                            parent.remove();
                        }
                    }
                },
                ({ data, responseCode, xhr, oc }) => {
                    if (parent) {
                        this.hideLoading(parent.querySelectorAll('[data-bloghub-handler="onChangeStatus"]'));
                    }
                    alert(typeof data === 'object'? data.result: data);
                }
            );
        }

        /**
         * Change Comment Status (Like, Dislike)
         * @param {HTMLElement} el 
         */
        onVote(el) {
            if (!el.dataset.bloghubVote || !el.dataset.bloghubId) {
                return false;
            }
            let parent = el.closest('[data-comment-id]');

            // Show Loading Indicator
            if (parent) {
                this.showLoading(parent.querySelectorAll('[data-bloghub-handler="onVote"]'));
            }
            
            // Call AJAX backend
            this.callOctober('onVote', {
                vote: el.dataset.bloghubVote,
                comment_id: el.dataset.bloghubId
            }).then(
                ({ data, responseCode, xhr, oc }) => {
                    if (parent) {
                        this.hideLoading(parent.querySelectorAll('[data-bloghub-handler="onVote"]'));
                        
                        if (data.comment) {
                            parent.replaceWith(this.stringToElement(data.comment));
                        } else {
                            parent.remove();
                        }
                    }
                },
                ({ data, responseCode, xhr, oc }) => {
                    if (parent) {
                        this.hideLoading(parent.querySelectorAll('[data-bloghub-handler="onVote"]'));
                    }
                    alert(typeof data === 'object'? data.result: data);
                }
            );
        }

        /**
         * Create Reply Form
         * @param {HTMLElement} el 
         */
        onCreateReply(el) {
            if (!el.dataset.bloghubId) {
                return false;
            }
            
            // Get Form
            let form = this.parent.querySelector('form');
            if (!form) {
                return false;
            }

            // Show Loading Indicator
            let parent = el.closest('[data-comment-id]');
            if (parent) {
                this.showLoading(parent.querySelectorAll('[data-bloghub-handler]'));
            }
            
            // Call AJAX backend
            this.callOctober('onCreateReply', {
                comment_id: el.dataset.bloghubId
            }).then(
                ({ data, responseCode, xhr }) => {
                    if (parent) {
                        this.hideLoading(parent.querySelectorAll('[data-bloghub-handler]'));
                    }
                    form.scrollIntoView({ behavior: "smooth" });
                    form.querySelector('button[type="submit"]').innerText = data.submitText;
                    form.querySelector('button[data-bloghub-handler="onCancelReply"]').style.removeProperty('display');

                    let hidden = document.createElement('INPUT');
                    hidden.type = 'hidden';
                    hidden.name = 'comment_parent';
                    hidden.value = data.comment.id;
                    form.appendChild(hidden);
                    form.insertBefore(this.stringToElement(data.reply), form.children[0]);
                },
                ({ data, responseCode, xhr }) => {
                    if (parent) {
                        this.hideLoading(parent.querySelectorAll('[data-bloghub-handler]'));
                    }
                    alert(typeof data === 'object'? data.result: data);
                }
            );
        }

        /**
         * Cancel Reply Form
         * @param {HTMLElement} el 
         */
        onCancelReply(el) {
            let form = this.parent.querySelector('form');
            if (!form) {
                return false;
            }

            // Show Loading Indicator
            this.showLoading(form.querySelectorAll('button'));
    
            // Call AJAX backend
            this.callOctober('onCancelReply', {}).then(
                ({ data, responseCode, xhr }) => {
                    this.hideLoading(form.querySelectorAll('button'));
                    form.querySelector('button[type="submit"]').innerText = data.submitText;
                    form.querySelector('button[data-bloghub-handler="onCancelReply"]').style.display = 'none';
                    
                    let replyTo = form.querySelector('.comment-form-reply-to');
                    if (replyTo) {
                        replyTo.remove();
                    }

                    let hidden = form.querySelector('input[name="comment_parent"]');
                    if (hidden) {
                        hidden.remove();
                    }
                },
                ({ data, responseCode, xhr }) => {
                    this.hideLoading(form.querySelectorAll('button'));
                    alert(typeof data === 'object'? data.result: data);
                }
            );
        }

        /**
         * Reload GREGWAR Captcha
         * @param {HTMLElement} el 
         */
        onReloadCaptcha(el) {
            let form = this.parent.querySelector('form');
            if (!form) {
                return false;
            }

            // Show Loading Indicator
            this.showLoading(form.querySelectorAll('button'));
            
            // Call AJAX backend
            this.callOctober('onReloadCaptcha', {}).then(
                ({ data, responseCode, xhr }) => {
                    this.hideLoading(form.querySelectorAll('button'));

                    let image = form.querySelector('img.comment-form-captcha');
                    if (image) {
                        image.src = data.captchaImage;
                    }
                },
                ({ data, responseCode, xhr }) => {
                    this.hideLoading(form.querySelectorAll('button'));
                    alert(typeof data === 'object'? data.result: data);
                }
            );
        }

        /**
         * Submit Comment or Reply
         * @param {HTMLElement} el 
         */
        onComment(el) {
            let form = this.parent.querySelector('form');
            if (!form) {
                return false;
            }

            // Show Loading Indicator
            this.showLoading(form.querySelectorAll('button'));

            // Call AJAX backend
            this.callOctober('onComment', Object.fromEntries([...(new FormData(form)).entries()])).then(
                ({ data, responseCode, xhr }) => {
                    this.hideLoading(form.querySelectorAll('button'));

                    let comments = this.stringToElement(data.comments);
                    this.parent.innerHTML = comments.innerHTML;
                },
                ({ data, responseCode, xhr }) => {
                    this.hideLoading(form.querySelectorAll('button'));

                    if (typeof data === 'object') {
                        let alert = this.createAlert('danger', data.message || data.X_OCTOBER_ERROR_MESSAGE);

                        let formAlert = form.querySelector('.alert');
                        if (formAlert) {
                            formAlert.replaceWith(alert);
                        } else {
                            form.insertBefore(alert, form.children[0]);
                        }

                        let image = form.querySelector('img.comment-form-captcha');
                        if (data.captchaImage && image) {
                            image.src = data.captchaImage;
                        }
                    } else {
                        alert(typeof data === 'object'? data.result: data);
                    }

                }
            );
        }
    }
    Array.from(document.querySelectorAll('[data-bloghub-comments]')).map(el => {
        new BlogHubComments(el);
    })

}));