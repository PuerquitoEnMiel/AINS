# Guía de Habilidades "Caveman" 🦴

Guía rápida para optimizar tokens y velocidad usando el ecosistema Caveman.

## Modos de Comunicación (/caveman)

Controlan cómo hablo contigo. El objetivo es eliminar "paja" y ahorrar dinero/tokens.

| Modo | Intensidad | Uso Ideal |
| :--- | :--- | :--- |
| `/caveman lite` | Baja | Mantengo gramática completa pero elimino frases de relleno y cortesías. Profesional y directo. |
| `/caveman full` | Media | **(Por defecto)** Estilo cavernícola clásico. Sin artículos (el/la), frases fragmentadas, sin preámbulos. Máximo ahorro sin perder claridad. |
| `/caveman ultra` | Alta | Abreviaturas extremas (auth, db, fn), flechas para causalidad (A → B). Solo para expertos en el proyecto. |

## Herramientas Especializadas

### 🛠️ Subagentes (`cavecrew`)
Delegación de tareas a sub-IA especializadas para no saturar la memoria principal.
- **Investigator**: Solo lectura. Para buscar dónde se define algo o cómo funciona un flujo.
- **Builder**: Para cambios quirúrgicos en 1 o 2 archivos. Rápido y preciso.
- **Reviewer**: Revisión rápida de archivos o diffs con emojis de severidad.

### 📝 Commits (`/caveman-commit`)
Genera mensajes de commit ultra-concisos (≤50 caracteres).
- **Uso**: Úsalo justo antes de confirmar cambios.
- **Ejemplo**: `fix(auth): add null check to user profile`

### 🔍 Revisiones (`/caveman-review`)
Comentarios de Pull Request en una sola línea.
- **Formato**: `[Archivo:Línea] [tipo]: [problema]. [solución].`
- **Ejemplo**: `auth.py:42 error: missing guard clause. Add if not user.`

### 🗜️ Compresión (`/caveman-compress`)
Comprime archivos de memoria (como `CLAUDE.md` o TODOs) al estilo caveman.
- **Uso**: `/caveman-compress [ruta/al/archivo]`
- **Resultado**: Reduce el tamaño del archivo un ~60% para que la IA lea menos tokens al iniciar sesión.

### 📊 Estadísticas (`/caveman-stats`)
Muestra cuánto dinero y tokens has ahorrado en la sesión actual.

---

## Reglas de Oro 🗿
1. **Acción primero**: Ejecuto el código antes de explicarlo.
2. **Sin anuncios**: No diré "Voy a leer el archivo...", simplemente lo leo.
3. **Código claro**: Si el código se explica solo, no añado texto.
4. **Errores directos**: Si algo falla, muestro el arreglo. No narro el drama del error.
