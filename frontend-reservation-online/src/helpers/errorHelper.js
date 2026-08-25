export function handleError(error) {
  if (!error.response) {
    return {
      message: "Impossible de joindre le serveur. Vérifiez votre connexion ou que le backend est démarré.",
      errors: {},
    };
  }

  const status = error.response.status;
  const data = error.response.data || {};

  if (status === 422) {
    return {
      message: data.message || "Erreur de validation des données.",
      errors: data.errors || {},
    };
  }

  if (status === 401) {
    return {
      message: data.message || "Identifiants invalides ou session expirée.",
      errors: {},
    };
  }

  if (status === 403) {
    return {
      message: data.message || "Action non autorisée.",
      errors: {},
    };
  }

  if (status >= 500) {
    return {
      message: data.message || "Une erreur serveur est survenue.",
      errors: {},
    };
  }

  return {
    message: data.message || "Une erreur est survenue.",
    errors: data.errors || {},
  };
}

