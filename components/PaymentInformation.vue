<template>
  <ion-list lines="none">
    <ion-item>
      <ion-grid>
        <ion-row>
          <ion-input
            fill="outline"
            label="Adresse"
            label-placement="floating"
            v-model="inputFields.Adresse"
          >
          </ion-input>
        </ion-row>
        <ion-row>
          <ion-col>
            <ion-input
              fill="outline"
              label="Postleitzahl"
              label-placement="floating"
              v-model="inputFields.Postleitzahl"
            >
            </ion-input>
          </ion-col>
          <ion-col>
            <ion-input
              fill="outline"
              label="Stadt"
              label-placement="floating"
              v-model="inputFields.Stadt"
            >
            </ion-input>
          </ion-col>
        </ion-row>
        <ion-row>
          <ion-col>
            <ion-input
              fill="outline"
              label="Vorname"
              label-placement="floating"
              v-model="inputFields.Vorname"
            >
            </ion-input>
          </ion-col>
          <ion-col>
            <ion-input
              fill="outline"
              label="Nachname"
              label-placement="floating"
              v-model="inputFields.Nachname"
            >
            </ion-input>
          </ion-col>
        </ion-row>
        <ion-row>
          <ion-col>
            <ion-input
              fill="outline"
              label="Telefon"
              label-placement="floating"
              type="tel"
              v-model="inputFields.Telefon"
            >
            </ion-input>
          </ion-col>
          <ion-col>
            <ion-input
              fill="outline"
              label="E-Mail"
              label-placement="floating"
              type="email"
              v-model="inputFields.Email"
            >
            </ion-input>
          </ion-col>
        </ion-row>
      </ion-grid>
    </ion-item>
  </ion-list>
</template>

<script setup>
import {
  IonItem,
  IonInput,
  IonList,
  IonGrid,
  IonRow,
  IonCol,
} from "@ionic/vue";

const props = defineProps({
  inputFields: {
    type: Object,
    required: true,
  },
});
</script>
