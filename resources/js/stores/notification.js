import { defineStore } from 'pinia';
import axios from 'axios';

export const useNotificationStore = defineStore('notification', {
    state: () => ({
        notifications: [],
    }),
    actions: {
        async fetchNotifications() {
            try {
                const response = await axios.get(route('admin.notifications.index'));
                this.notifications = response.data.notifications;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        },
        async markAsRead(notificationId) {
            try {
                await axios.patch(route('admin.notifications.mark-as-read', notificationId));
                const index = this.notifications.findIndex((n) => n.id === notificationId);
                if (index !== -1) {
                    this.notifications[index].read_at = new Date().toISOString();
                }
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },
    }
});
