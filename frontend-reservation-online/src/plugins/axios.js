import router from "@/router";
import axios from "axios";


const axiosClient = axios.create({
      baseURL : import.meta.env.BASE_URL_API,
      withXSRFToken : true,
      withCredentials : true,
})

axiosClient.interceptors.response.use( (response) => {
  return response ;
} ,  error => {
  if(error.response && error.response.status === 401) {
      router.push({name : 'Login'});
  }

  throw error ;
});

export default axiosClient ;