import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
const plugin = require("tailwindcss/plugin");

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/*.js',
        './node_modules/@inertiaui/modal-vue/src/**/*.{js,vue}',
    ],
    darkMode: "class", // or 'media' or 'class'
    theme: {
        asideScrollbars: {
            light: "light",
            gray: "gray",
        },
        extend: {
            fontFamily: {
                inter: ["Inter", "sans-serif"],
                poppins: ["Poppins", "sans-serif"],
            },
            inset: {
                '3/5': '60%',
                '2/5': '40%',
            },
            width: {
                '7/20': '35%',
            },
            colors: {
                p1: '#2EF2FF',
                p2: '#3C52D9',
                p3: '#C8EA80',
                p4: '#EAEDFF',
                p5: '#C4CBF5',
                s0: '#010413',
                s1: '#080D27',
                s2: '#0f1536',
                s3: '#324679',
                s4: '#1959AD',
                s5: '#263466',
                s6: '#3c4684',
                s7: '#4957a1',
                s8: '#192146',
                black: {
                    DEFAULT: '#000000',
                    100: '#05091D',
                },
            },
            boxShadow: {
                100: '0px 4px 4px rgba(0, 0, 0, 0.25), 0px 16px 24px rgba(0, 0, 0, 0.25), inset 0px 3px 6px #1959AD',
                200: '0px 4px 4px rgba(0, 0, 0, 0.25), 0px 16px 24px rgba(0, 0, 0, 0.25), inset 0px 4px 10px #3391FF',
                300: '0px 4px 4px rgba(0, 0, 0, 0.25), 0px 16px 24px rgba(0, 0, 0, 0.25), inset 0px 3px 6px #1959AD',
                400: 'inset 0px 2px 4px 0 rgba(255, 255, 255, 0.05)',
                500: '0px 16 b.                        n.   px 24px rgba(0, 0, 0, 0.25), 0px -14px 48px rgba(40, 51, 111, 0.7)',
                600: '0px 16px 24px rgba(0, 0, 0, 0.25), 0px -8 px 28px rgba(40, 51, 111, 0.7)',
            },
            zIndex: {
                "-1": "-1",
                "4": "4",
            },
            flexGrow: {
                5: "5",
            },
            maxHeight: {
                "screen-menu": "calc(100vh - 3.5rem)",
                modal: "calc(100vh - 160px)",
            },
            transitionProperty: {
                position: "right, left, top, bottom, margin, padding",
                textColor: "color",
            },
            keyframes: {
                "fade-out": {
                    from: { opacity: 1 },
                    to: { opacity: 0 },
                },
                "fade-in": {
                    from: { opacity: 0 },
                    to: { opacity: 1 },
                },
            },
            animation: {
                "fade-out": "fade-out 250ms ease-in-out",
                "fade-in": "fade-in 250ms ease-in-out",
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        plugin(function ({ matchUtilities, theme }) {
            matchUtilities(
                {
                    "aside-scrollbars": (value) => {
                        const track = value === "light" ? "100" : "900";
                        const thumb = value === "light" ? "300" : "600";
                        const color = value === "light" ? "gray" : value;

                        return {
                            scrollbarWidth: "thin",
                            scrollbarColor: `${theme(`colors.${color}.${thumb}`)} ${theme(
                                `colors.${color}.${track}`
                            )}`,
                            "&::-webkit-scrollbar": {
                                width: "8px",
                                height: "8px",
                            },
                            "&::-webkit-scrollbar-track": {
                                backgroundColor: theme(`colors.${color}.${track}`),
                            },
                            "&::-webkit-scrollbar-thumb": {
                                borderRadius: "0.25rem",
                                backgroundColor: theme(`colors.${color}.${thumb}`),
                            },
                        };
                    },
                },
                { values: theme("asideScrollbars") }
            );
        }),
    ],
};
