// axios.js
import axios from 'axios';
import router from '@/router';

const instance = axios.create({
    baseURL: "/api",

    //timeout: 5000, Set a timeout for requests (optional)
    headers: {
        'Content-Type': 'application/json'
    },
});

// Add an interceptor to handle 401 Unauthorized responses
instance.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // If response status is 401 Unauthorized, redirect to login
        if (error.response && error.response.status === 401) {
            router.push('/'); // Redirect to login page
        }
        return Promise.reject(error);
    }
);

export default instance;
