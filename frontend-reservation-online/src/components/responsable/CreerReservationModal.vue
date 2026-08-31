<script setup>
import { ref, reactive, watch } from "vue";
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from "@headlessui/vue";
import { Plus, Trash2, X, Loader2 } from "@lucide/vue";
import catalogueService from "@/services/catalogueService";
import { useResponsableReservationsStore } from "@/store/responsableReservations";
import { useToast } from "@/composables/useToast";

const props = defineProps({
  open: Boolean,
});
const emit = defineEmits(["fermer"]);

const store = useResponsableReservationsStore();
const toast = useToast();

const salles = ref([]);
const equipementsDisponibles = ref([]);

const form = reactive({
  nom_client: "",
  telephone_client: "",
  salle_id: "",
  date_heure_debut: "",
  date_heure_fin: "",
  nombre_personnes: 1,
  equipements: [],
});

function reinitialiserFormulaire() {
  form.nom_client = "";
  form.telephone_client = "";
  form.salle_id = "";
  form.date_heure_debut = "";
  form.date_heure_fin = "";
  form.nombre_personnes = 1;
  form.equipements = [];
  store.creationErrors = {};
}

watch(() => props.open, (estOuvert) => {
  if (estOuvert) {
    reinitialiserFormulaire();
    chargerCatalogue();
  }
});

async function chargerCatalogue() {
  const [reponseSalles, reponseEquipements] = await Promise.all([
    catalogueService.fetchSalles(),
    catalogueService.fetchEquipements(),
  ]);
  salles.value = reponseSalles.data.data;
  equipementsDisponibles.value = reponseEquipements.data.data;
}

function ajouterLigneEquipement() {
  form.equipements.push({ equipement_id: "", quantity: 1 });
}

function retirerLigneEquipement(index) {
  form.equipements.splice(index, 1);
}

async function soumettre() {
  const payload = {
    ...form,
    equipements: form.equipements.filter((e) => e.equipement_id),
  };

  const succes = await store.creerManuelle(payload);
  if (succes) {
    toast.success("Réservation créée avec succès.");
    emit("fermer");
  } else {
    toast.error("Vérifiez les informations saisies.");
  }
}
</script>

<template>
  <TransitionRoot appear :show="open" as="template">
    <Dialog as="div" class="relative z-50" @close="emit('fermer')">
      <TransitionChild enter="duration-200 ease-out" enter-from="opacity-0" enter-to="opacity-100"
        leave="duration-150 ease-in" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <TransitionChild enter="duration-200 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100"
          leave="duration-150 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
          <DialogPanel class="w-full max-w-lg bg-white rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <DialogTitle class="text-base font-semibold text-slate-900">
                Nouvelle réservation manuelle
              </DialogTitle>
              <button type="button" class="text-slate-400 hover:text-slate-600" @click="emit('fermer')">
                <X class="w-5 h-5" />
              </button>
            </div>

            <p class="text-sm text-slate-500 mb-5">
              Pour un client sans compte sur la plateforme (ex : réservation par téléphone).
            </p>

            <form @submit.prevent="soumettre" class="space-y-4">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-slate-600 mb-1">Nom du client</label>
                  <input v-model="form.nom_client" type="text" minlength="2" maxlength="100" pattern="^[\p{L}\s'-]+$"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                  <p v-if="store.creationErrors.nom_client" class="mt-1 text-xs text-red-600">
                    {{ store.creationErrors.nom_client[0] }}
                  </p>
                </div>
                <div>
                  <label class="block text-xs font-medium text-slate-600 mb-1">Téléphone</label>
                  <input v-model="form.telephone_client" type="tel" pattern="^[0-9+\s().-]{8,20}$" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                  <p v-if="store.creationErrors.telephone_client" class="mt-1 text-xs text-red-600">
                    {{ store.creationErrors.telephone_client[0] }}
                  </p>
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Salle</label>
                <select v-model="form.salle_id" required
                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="" disabled>Choisir une salle</option>
                  <option v-for="salle in salles" :key="salle.id" :value="salle.id">
                    {{ salle.nom }} (capacité {{ salle.capacite }})
                  </option>
                </select>
                <p v-if="store.creationErrors.salle_id" class="mt-1 text-xs text-red-600">
                  {{ store.creationErrors.salle_id[0] }}
                </p>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-slate-600 mb-1">Début</label>
                  <input v-model="form.date_heure_debut" type="datetime-local" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-slate-600 mb-1">Fin</label>
                  <input v-model="form.date_heure_fin" type="datetime-local" :min="form.date_heure_debut || undefined"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
              </div>
              <p v-if="store.creationErrors.date_heure_debut || store.creationErrors.date_heure_fin"
                class="text-xs text-red-600">
                {{ (store.creationErrors.date_heure_debut || store.creationErrors.date_heure_fin)[0] }}
              </p>

              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nombre de personnes</label>
                <input v-model.number="form.nombre_personnes" type="number" min="1" max="500" required
                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="store.creationErrors.nombre_personnes" class="mt-1 text-xs text-red-600">
                  {{ store.creationErrors.nombre_personnes[0] }}
                </p>
              </div>

              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-medium text-slate-600">Équipements (optionnel)</label>
                  <button type="button"
                    class="flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700"
                    @click="ajouterLigneEquipement">
                    <Plus class="w-3.5 h-3.5" /> Ajouter
                  </button>
                </div>

                <div v-for="(ligne, index) in form.equipements" :key="index" class="flex gap-2 mb-2">
                  <select v-model="ligne.equipement_id"
                    class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="" disabled>Choisir un équipement</option>
                    <option v-for="eq in equipementsDisponibles" :key="eq.id" :value="eq.id">
                      {{ eq.nom }}
                    </option>
                  </select>
                  <input v-model.number="ligne.quantity" type="number" min="1" max="1000"
                    class="w-20 rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                  <button type="button" class="text-slate-400 hover:text-red-600"
                    @click="retirerLigneEquipement(index)">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
                <p v-if="store.creationErrors.equipements" class="mt-1 text-xs text-red-600">
                  {{ store.creationErrors.equipements[0] }}
                </p>
              </div>

              <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                  class="px-4 py-2 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition"
                  @click="emit('fermer')">
                  Annuler
                </button>
                <button type="submit" :disabled="store.creationLoading"
                  class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                  <Loader2 v-if="store.creationLoading" class="w-4 h-4 animate-spin" />
                  {{ store.creationLoading ? "Création…" : "Créer la réservation" }}
                </button>
              </div>
            </form>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
