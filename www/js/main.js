(function() {
    // Taken from https://stackoverflow.com/a/274094 because I'm lazy
    String.prototype.regexIndexOf = function(regex, startpos) {
        var indexOf = this.substring(startpos || 0).search(regex);
        return (indexOf >= 0) ? (indexOf + (startpos || 0)) : indexOf;
    }

    function getCookie(cname) {
        var cookies = decodeURIComponent(document.cookie.replace(/\+/g, ' ')).split(';');
        var start_index = 0;
        for (var i=0; i<cookies.length; ++i) {
            var c = cookies[i];
            while (c[start_index] === ' ')
                ++start_index;

            c = c.substr(start_index);
            if (c.indexOf(cname + '=') === 0) {
                return c.substr(cname.length + 1);
            }
        }

        return '';
    }

    function zeroPadNumber(num) {
        if (parseInt(num, 10) < 10) {
            return '0' + num.toString();
        }

        return num.toString();
    }

    function getDayShortText(day) {
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        return days[day];
    }

    function getMonthShortText(month) {
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[month];
    }

    $$(window).one('load', function(e) {
        $$('.post-date').each(function(element) {
            var t = new Date(parseInt(element.dataset.time, 10) * 1000);
            var day = getDayShortText(t.getDay());
            var month = getMonthShortText(t.getMonth());
            var hours = t.getHours();
            var am_pm = 'AM';
            if (hours > 12) {
                am_pm = 'PM';
                hours = hours % 12;
            }
            else if (hours === 12) {
                am_pm = 'PM';
            }
            else if (hours === 0) {
                hours = 12;
            }

            element.innerText = (day + ' ' +
                t.getDate().toString() + '/' + month + '/' + t.getFullYear().toString() + ' ' +
                zeroPadNumber(hours) + ':' + zeroPadNumber(t.getMinutes()) + ':' + zeroPadNumber(t.getSeconds()) + ' ' + am_pm);
        });

        $$('.post-body').each(function(element) {
            var text = element.innerText;
            var html = '';
            var start_index = 0;
            var index = text.indexOf('>');
            while (index > -1) {
                if (index < (text.length - 1) && text[index + 1] === '>') {
                    // ID link
                    var white_space_index = text.regexIndexOf(/\s/, index);
                    if (white_space_index === -1)
                        white_space_index = text.length;
                    var id = text.substring(index + 2, white_space_index);
                    var link;
                    if (element.parentElement.dataset.parentId !== undefined) {
                        link = '<a href="/thread/' + element.parentElement.dataset.parentId + '#' + id + '">&gt;&gt;' + id + '</a>';
                    }
                    else {
                        link = '<a href="#' + id + '">' + '&gt;&gt;' + id + '</a>';
                    }
                    html += text.substring(start_index, index) + link;
                    start_index = white_space_index;
                }
                else {
                    // Greentext
                    var newline_index = text.indexOf('\n', index);
                    if (newline_index === -1)
                        newline_index = text.length;
                    var greentext = text.substring(index + 1, newline_index);
                    html += text.substring(start_index, index) + '<span class="greentext">&gt;' + greentext + '</span>';
                    start_index = newline_index;
                }

                index = text.indexOf('>', start_index);
            }

            if (start_index < text.length - 1)
                html += text.substring(start_index, text.length);

            html = html.replace(/\n/g, '<br>');
            element.innerHTML = html;
        });

        $$('a[href="#"]').each(function(element) {
            $$(element).on('click', function(event) {
                $$('#txt-message').first().get().textContent += '>>' + element.dataset.id + '\n';
            });
        });

        $$('#txt-name').val(getCookie('slchan_name'));
        $$('#txt-password').val(getCookie('slchan_pass'));
        $$('#txt-delete-password').val($$('#txt-password').val());

        $$('#btn-delete').on('click', function(event) {
            $$('input[name="delete_id"]').each(function(element) {
                if (element.checked) {
                    var id = element.value;
                    var password = $$('#txt-delete-password').val();
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/delete';

                    var input = document.createElement('input');
                    input.name = 'id';
                    input.value = id;
                    form.appendChild(input);

                    input = document.createElement('input');
                    input.name = 'password';
                    input.value = password;
                    form.appendChild(input);

                    document.body.appendChild(form);
                    form.submit();
                    return true;
                }
            });
        });
    });
}());
