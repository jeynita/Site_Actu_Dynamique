// ============================================================
// js/validation.js -- Fonctions de validation réutilisables
// ============================================================
/**
 * Valide qu'un champ n'est pas vide
 * @param {string} id - ID du champ
 * @param {string} errId - ID du span erreur
 * @param {string} msg - Message d'erreur
 * @returns {boolean}
 */
function validerChamp(id, errId, msg) {
  const val = document.getElementById(id)?.value.trim();
  const err = document.getElementById(errId);
  if (err) err.textContent = "";
  if (!val) {
    if (err) err.textContent = msg;
    return false;
  }
  return true;
}
/**
 * Valide que deux mots de passe correspondent
 */
function validerMotsDePasse(id1, id2, errId) {
  const v1 = document.getElementById(id1)?.value;
  const v2 = document.getElementById(id2)?.value;
  const err = document.getElementById(errId);
  if (err) err.textContent = "";
  if (v1.length < 6) {
    if (err) err.textContent = "Minimum 6 caracteres requis.";
    return false;
  }
  if (v1 !== v2) {
    if (err) err.textContent = "Les mots de passe ne correspondent pas.";
    return false;
  }
  return true;
}
/**
 * Reinitialise tous les messages d'erreur JS d'un formulaire
 */
function reinitialiserErreurs(formId) {
  const form = document.getElementById(formId);
  if (!form) return;
  form.querySelectorAll(".erreur-js").forEach((el) => (el.textContent = ""));
}
