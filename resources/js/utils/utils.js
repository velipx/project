import moment from 'moment';
import FormField from "@/Components/Admin/FormField.vue";
import FormControl from "@/Components/Admin/FormControl.vue";

const DEFAULT_DATE_FORMAT = 'DD. MMMM YYYY';

export function clearFormFieldError(form, field) {
    if (!form.errors[field]) return;
    form.errors[field] = null;
}

export function formatDate(date, format = null) {
    if (!date) return 'N/A';

    if(!format) {
        return moment(date).fromNow();
    }

    return moment(date).format(format);
}

export function formatNumber(num) {
    if (num >= 1000) {
        return `${(num / 1000).toFixed(1)}k`;
    }
    return num;
};

export function navigateBack() {
    return window.history.back();
}
