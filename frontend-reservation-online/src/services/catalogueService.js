import axiosClient from "@/plugins/axios";

export default {
  fetchSalles() {
    return axiosClient.get("/salles");
  },
  fetchEquipements() {
    return axiosClient.get("/equipements");
  },
};
