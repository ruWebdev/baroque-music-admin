// @ts-check
import { defineStore } from 'pinia'

export const useUserSettings = defineStore('userSettings', {
    state: () => ({
        userPic: 'https://i0.wp.com/www.repol.copl.ulaval.ca/wp-content/uploads/2019/01/default-user-icon.jpg?ssl=1',
        currentPage: null,
        filters: {
            orders: {
                internal_code: null,
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                buyer_id: 0,
                buyer_manager_id: 0,
                payment_type: 0,
            },
            deliveries: {
                internal_code: null,
                delivery_type: null,
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                buyer_id: 0,
                buyer_manager_id: 0,
                driver_id: 0,
                vehicle_id: 0,
                is_paid: null,
                discounts: 0,
            },
            arrivals: {
                internal_code: null,
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                buyer_id: 0,
                buyer_manager_id: 0,
                payment_type: 0,
            },
            marina: {
                client_id: null,
                start_date: '2024-01-01',
                end_date: '2024-01-01',
            },
            products: {
                type_id: 0,
                last_price_date: null,
            },
            preorders_report: {
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                product_type: 0,
                buyer_id: 0,
                buyer_manager_id: 0,
            },
            sales_report: {
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                product_type: 0,
                buyer_id: 0,
                buyer_manager_id: 0,
            },
            arrivals_report: {
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                product_type: 0,
            },
            stock_report: {
                start_date: '2024-01-01',
                end_date: '2024-01-01',
                product_type: 0,
                quantity_type: 0,
            }
        }
    }),
    persist: true,
    actions: {},
})
