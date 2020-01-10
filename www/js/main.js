(function() {
    // Taken from https://stackoverflow.com/a/274094 because I'm lazy
    String.prototype.regexIndexOf = function(regex, startpos) {
        var indexOf = this.substring(startpos || 0).search(regex);
        return (indexOf >= 0) ? (indexOf + (startpos || 0)) : indexOf;
    }

    function zeroPadNumber(num) {
        if (parseInt(num, 10) < 10) {
            return '0' + num.toString();
        }

        return num.toString();
    }

    function getDayShortText(day) {
        switch (day) {
            case 0:
                return 'Mon';
            case 1:
                return 'Tue';
            case 2:
                return 'Wed';
            case 3:
                return 'Thu';
            case 4:
                return 'Fri';
            case 5:
                return 'Sat';
            case 6:
                return 'Sun';
        }

        return 'Unknown';
    }

    function getMonthShortText(month) {
        switch (month) {
            case 0:
                return 'Jan';
            case 1:
                return 'Feb';
            case 2:
                return 'Mar';
            case 3:
                return 'Apr';
            case 4:
                return 'May';
            case 5:
                return 'Jun';
            case 6:
                return 'Jul';
            case 7:
                return 'Aug';
            case 8:
                return 'Sep';
            case 9:
                return 'Oct';
            case 10:
                return 'Nov';
            case 11:
                return 'Dec';
        }

        return 'Unknown';
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
                        link = '<a href="#">' + '&gt;&gt;' + id + '</a>';
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
                if (element.innerText.indexOf('>>') === 0) {
                    var href = document.location.href;
                    document.location.href = href.substring(0, href.indexOf('#')) + '#' + element.innerText.substr(2);
                }
                else {
                    $$('#txt-message').first().get().textContent += '>>' + element.dataset.id + '\n';
                }
            });
        });
    });
}());
