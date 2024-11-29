<template>
  <ion-list lines="none">
    <ion-item>
      <ion-grid>
        <ion-row>
          <ion-col>
            <ion-input
              fill="outline"
              label="Adresse"
              label-placement="floating"
              v-model="inputFields.Adresse"
            >
            </ion-input>
          </ion-col>
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
              placeholder="0000 0000 0000"
              v-maskito="phoneOptions"
              maxlength="14"
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
              error-text="Ungültige E-Mail"
              @ionInput="validate"
              @ionBlur="markTouched"
              ref="emailInputRef"
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
import { maskito as vMaskito } from "@maskito/vue";
import { ref } from "vue";

const props = defineProps({
  inputFields: {
    type: Object,
    required: true,
  },
});

const emailInputRef = ref(null);

const validateEmail = (email) => {
  return (
    email.match(
      /^(?=.{1,254}$)(?=.{1,64}@)[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z]{2,63})$/
    ) !== null
  );
};

const validate = (ev) => {
  const target = ev.target;
  const value = target.value;

  const inputEl = emailInputRef.value?.$el;
  if (!inputEl) return;

  inputEl.classList.remove("ion-valid", "ion-invalid");

  if (value === "") return;

  validateEmail(value)
    ? inputEl.classList.add("ion-valid")
    : inputEl.classList.add("ion-invalid");
};

const markTouched = () => {
  const inputEl = emailInputRef.value?.$el;
  if (inputEl) {
    inputEl.classList.add("ion-touched");
  }
};

const phoneOptions = {
  mask: [
    ...Array(4).fill(/\d/),
    " ",
    ...Array(4).fill(/\d/),
    " ",
    ...Array(5).fill(/\d/),
    " ",
  ],
  elementPredicate: (el) => {
    return new Promise((resolve) => {
      requestAnimationFrame(async () => {
        const input = await el.getInputElement();
        resolve(input);
      });
    });
  },
};
</script>
