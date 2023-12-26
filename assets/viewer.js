import './styles/viewer.css';
import $ from "jquery";

import 'lightbox2/dist/css/lightbox.min.css';
import lightbox2 from 'lightbox2/dist/js/lightbox.js';
import {arrow, computePosition, flip, offset, shift} from '@floating-ui/dom';

$(document).ready(function () {

    $('body').fadeIn(1000);

    $(".scroll-to").click(function (e) {
        e.preventDefault();
        const targetUuid = $(this).data('scroll-to');
        const targetElement = $('#node-' + targetUuid);
        $('html, body').animate({
            scrollTop: targetElement.offset().top - 50
        }, 500);
    });

    const pageAmountScrolled = 300;

    $(window).scroll(function () {
        if ($(window).scrollTop() > pageAmountScrolled) {
            $("#toTopWrapper").fadeIn(250);
        } else {
            $("#toTopWrapper").fadeOut(250);
        }
    });

    $("#toTop").click(function (e) {
        e.preventDefault();
        $("html, body").animate({scrollTop: 0}, 500);
    });

    /* -- make some lightboxes -------------------------------------------------------------------------------------- */

    lightbox2.option({
        'resizeDuration': 100,
        'wrapAround': true
    });

    $(".box-switch").on("click", () => {
        $("body").toggleClass("dark");
    });

    /* -- make some figma iframes ----------------------------------------------------------------------------------- */

    const linkElements = document.querySelectorAll('a');
    if (linkElements) {
        linkElements.forEach(function (linkElement) {
            if (linkElement.href.includes('https://www.figma.com/')) {
                let iframeElement = document.createElement('iframe');
                iframeElement.height = '450';
                iframeElement.width = '100%';
                iframeElement.src = 'https://www.figma.com/embed?embed_host=locodio&url=' + linkElement.href;
                iframeElement.allowFullscreen = true;
                linkElement.parentNode.replaceChild(iframeElement, linkElement);
            }
        });
    }

    /* -- make some tooltips ---------------------------------------------------------------------------------------- */

    const buttons = $('.button-tooltip');
    const titles = $('.anchor-tooltip');

    const elementsButtons = {};
    const elementsTooltips = {};
    const elementsArrows = {};
    const elementsAnchors = {};
    const elementsAnchorsHover = {};
    const locationHref = location.href;

    titles.each(function (index, element) {
        const currentTitle = $(this);
        const uuid = currentTitle.data('uuid');
        const number = currentTitle.data('number');
        elementsAnchors[uuid] = document.querySelector('#anchor-' + uuid);
        elementsAnchorsHover[uuid] = document.querySelector('#a-' + uuid);

        currentTitle.mouseover(function (e) {
            e.preventDefault();
            elementsAnchors[uuid].style.display = 'block';
            updatePositionAnchor(elementsAnchorsHover[uuid], elementsAnchors[uuid]);
        });

        currentTitle.mouseout(function (e) {
            e.preventDefault();
            elementsAnchors[uuid].style.display = 'none';
        });

        currentTitle.click(function (e) {
            e.preventDefault();
            if (history.pushState) {
                history.pushState(null, null, '#' + number);
            } else {
                window.location.hash = number;
            }
        });

    });

    buttons.each(function (index, element) {
        const currentButton = $(this);
        const uuid = currentButton.data('uuid');

        elementsButtons[uuid] = document.querySelector('#button-' + uuid);
        elementsTooltips[uuid] = document.querySelector('#tooltip-' + uuid);
        elementsArrows[uuid] = document.querySelector('#arrow-' + uuid);

        currentButton.mouseover(function (e) {
            e.preventDefault();
            elementsTooltips[uuid].style.display = 'block';
            updatePosition(elementsButtons[uuid], elementsTooltips[uuid], elementsArrows[uuid]);
        });

        currentButton.mouseout(function (e) {
            e.preventDefault();
            elementsTooltips[uuid].style.display = 'none';
        });
    });
});

function updatePosition(button, tooltip, arrowElement) {

    computePosition(button, tooltip, {
        placement: 'top',
        middleware: [
            offset(6),
            flip(),
            shift({padding: 5}),
            arrow({element: arrowElement}),
        ],
    }).then(({x, y, placement, middlewareData}) => {
        Object.assign(tooltip.style, {
            left: `${x}px`,
            top: `${y}px`,
        });

        const {x: arrowX, y: arrowY} = middlewareData.arrow;
        const staticSide = {
            top: 'bottom',
            right: 'left',
            bottom: 'top',
            left: 'right',
        }[placement.split('-')[0]];

        Object.assign(arrowElement.style, {
            left: arrowX != null ? `${arrowX}px` : '',
            top: arrowY != null ? `${arrowY}px` : '',
            right: '',
            bottom: '',
            [staticSide]: '-4px',
        });

    });
}

function updatePositionAnchor(button, tooltip) {
    computePosition(button, tooltip, {
        placement: 'left',
        middleware: [
            offset(6),
            flip(),
            shift({padding: 5}),
        ],
    }).then(({x, y}) => {
        Object.assign(tooltip.style, {
            left: `${x}px`,
            top: `${y}px`,
        });
    });
}
