;(function (factory) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', factory);
    } else {
        factory();
    }
}(function () {
    const oc = window.oc;

    class BlogHubComments {

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

        onLike(el) {

        }

        onDislike(el) {

        }

        onFavorite(el) {

        }

        onReply(el) {
            oc.ajax('onReply', {
                data: {
                    'form_id': el.dataset.bloghubId,
                    'comment_id': el.dataset.bloghubCommentId
                },
                success: function(data, responseCode, xhr) {
                    let form = document.getElementById(el.dataset.bloghubId + '_form');

                    if (form) {
                        form.scrollIntoView({ behavior: "smooth" });
                        form.querySelector('button[type="button"]').style.removeProperty('display');
                        form.querySelector('button[type="submit"]').innerText = data.submitButtonText;
                        this.success(data, responseCode, xhr);
                    }
                },
                error: function(data, responseCode, xhr) {
                    console.log(arguments);
                }
            });
        }

        onCancelReply(el) {

        }

        onSubmit(el) {

        }

    }

    new BlogHubComments();

    /**
     * Create a new Alert message box
     * @param {string} type 
     * @param {string|HTMLElement} content 
     * @returns 
     */
    function createAlert(type, content)
    {
        let alert = document.createElement('DIV');
        alert.className = `alert alert-${type}`;
        
        if (content instanceof HTMLElement) {
            alert.appendChild(content);
        } else {
            alert.innerHTML = content;
        }

        return alert;
    }

}));