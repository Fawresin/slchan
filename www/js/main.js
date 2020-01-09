(function() {
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

        $$('a[href="#"]').each(function(element) {
            $$(element).on('click', function(event) {
                $$('#txt-message').first().get().textContent += '>>' + element.dataset.id + ' ';
            });
        });
    });
}());
