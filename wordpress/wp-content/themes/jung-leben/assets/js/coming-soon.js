/**
 * Countdown für Jung Leben.
 */

document.addEventListener("DOMContentLoaded", () => {
    const countdown = document.querySelector(
        "[data-countdown]"
    );

    if (!countdown) {
        return;
    }

    const launchValue =
        countdown.dataset.launch;

    if (!launchValue) {
        return;
    }

    const launchDate =
        new Date(launchValue);

    const daysElement =
        countdown.querySelector(
            "[data-countdown-days]"
        );

    const hoursElement =
        countdown.querySelector(
            "[data-countdown-hours]"
        );

    const minutesElement =
        countdown.querySelector(
            "[data-countdown-minutes]"
        );

    const secondsElement =
        countdown.querySelector(
            "[data-countdown-seconds]"
        );

    const formatNumber = (value) => {
        return String(value).padStart(
            2,
            "0"
        );
    };

    const updateCountdown = () => {
        const now = new Date();

        const difference =
            launchDate.getTime()
            - now.getTime();

        if (difference <= 0) {
            daysElement.textContent = "00";
            hoursElement.textContent = "00";
            minutesElement.textContent = "00";
            secondsElement.textContent = "00";

            window.setTimeout(() => {
                window.location.reload();
            }, 1000);

            return false;
        }

        const totalSeconds =
            Math.floor(
                difference / 1000
            );

        const days =
            Math.floor(
                totalSeconds / 86400
            );

        const hours =
            Math.floor(
                (totalSeconds % 86400)
                / 3600
            );

        const minutes =
            Math.floor(
                (totalSeconds % 3600)
                / 60
            );

        const seconds =
            totalSeconds % 60;

        daysElement.textContent =
            formatNumber(days);

        hoursElement.textContent =
            formatNumber(hours);

        minutesElement.textContent =
            formatNumber(minutes);

        secondsElement.textContent =
            formatNumber(seconds);

        return true;
    };

    updateCountdown();

    const interval =
        window.setInterval(() => {
            const shouldContinue =
                updateCountdown();

            if (!shouldContinue) {
                window.clearInterval(
                    interval
                );
            }
        }, 1000);
});