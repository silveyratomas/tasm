# FoodExpress - guia rapida y en palabras simples

Que hay en el repo

- `tasm_index.php` - la pagina principal con el menu y filtro por categoria.
- `tasm_cart.php` - gestiona el carrito (POST via ajax) y muestra el carrito si abris la pagina.
- `tasm_checkout.php` - formulario de checkout y codigo que guarda pedidos en la BD.
- `tasm_admin.php` - panel basico para crear y eliminar productos (ahora protegido por login simple).
- `tasm_db.php` - conexion PDO y funciones para traer productos y categorias.
- `tasm_assets/` - `css/tasm_styles.css` y `js/tasm_app.js` para interaccion y estilos.
- `tasm_create_db.sql` - script para crear la base `tasm_foodexpress`, tablas y 10 productos de ejemplo.
- `tasm_test_db.php` - script rapido que muestra si la conexion funciona y cuantos productos hay.

## Explicacion tecnica - como se hizo y por que

- Arquitectura general

   Este proyecto es muy simple: tiene codigo en PHP que corre en el servidor (Apache/PHP) y una base de datos MySQL. La idea es mantener la logica de negocio en el servidor y usar un poquito de JavaScript solo para mejorar la experiencia (no para depender totalmente de el).

- Prefijo "tasm_"

   Usamos el prefijo `tasm_` en todo (archivos, funciones, tablas) porque lo pediste. Eso ayuda a que nada choque con otras cosas en el servidor y facilita buscar las piezas relacionadas.

- Como fluye una orden (paso a paso)

   1) El usuario ve el menu en `tasm_index.php`. Cada producto tiene un boton "Anadir".
   2) Cuando clickea "Anadir" el JavaScript (`tasm_app.js`) manda una peticion POST a `tasm_cart.php` con action=add y el id del producto.
   3) `tasm_cart.php` recibe la peticion y actualiza el carrito que esta guardado en la sesion PHP (`$_SESSION['tasm_cart']`). Asi mantenemos el carrito en el servidor sin necesidad de base de datos hasta que se finaliza la compra.
   4) El contador del carrito en la barra se actualiza con la respuesta JSON y el usuario puede ir al carrito (pagina que muestra lo que hay en la sesion).
   5) En el carrito, si el usuario cambia cantidades y presiona "Ir a pagar", el formulario manda las cantidades al `tasm_checkout.php`, este script actualiza la sesion con las cantidades nuevas y muestra el formulario de cliente.
   6) Cuando el usuario completa nombre/direccion y envia, `tasm_checkout.php` crea el pedido: inserta una fila en `tasm_orders` y varias filas en `tasm_order_items` dentro de una transaccion. Luego borra la sesion del carrito.

   Esta secuencia mantiene las cosas sencillas y evita inconsistencias: la transaccion al guardar el pedido asegura que o se graban todas las lineas o no se graba nada.

- Por que usar sesion para el carrito

   - Es facil: no hay que crear tablas ni loguear usuarios para empezar.
   - Funciona offline mientras dure la sesion del navegador (en local) y es rapido porque no hacemos consultas a BD cada vez que se anade un producto.
   - Para una aplicacion real con usuarios registrados se migraria el carrito a BD y se ligaria al usuario.

- Seguridad basica y decisiones de implementacion

   - Todas las consultas a la BD usan prepared statements (PDO) en `tasm_db.php` y en los insert del checkout. Eso reduce riesgo de SQL injection.
   - Para el guardado del pedido se uso una transaccion: es importante para que no queden pedidos a medias.
   - El panel admin tiene ahora un login basico que usa `tasm_admin_conf.php`. Esto es minimo para desarrollo; la solucion segura es guardar admins en BD usando `password_hash` y `password_verify`.

- Por que un poquito de JavaScript y no todo en JS

   - Hice la logica critica (guardar pedidos, calculos, reglas) en el servidor para no confiar en el cliente.
   - JS se usa solo para mejorar la UX: hacer la accion de "Anadir" sin recargar la pagina y actualizar el contador. Si JS falla, el sitio todavia funciona en modo basico (form posts).

- Estructura de archivos y responsabilidades

   - `tasm_db.php`: conexion PDO y funciones para traer productos/categorias. Toda la logica de consulta esta aca para no repetir codigo.
   - `tasm_index.php`: capa de presentacion del menu y llamadas JS para anadir productos.
   - `tasm_cart.php`: endpoint que actualiza la sesion (add, remove, update) y tambien muestra el carrito si abris la pagina.
   - `tasm_checkout.php`: actualiza cantidades y guarda el pedido en la BD.
   - `tasm_admin.php` + login: crear/eliminar productos (admin muy simple para desarrollo).
   - `tasm_assets/`: estilos mobile-first y JS minimo.

- Manejo de errores y casos borde

   - Si la BD no responde, `tasm_db.php` muestra un mensaje de error y corta la ejecucion: eso hace facil ver problemas en local.
   - Si llega un id de producto invalido al carrito, el servidor lo ignora o devuelve error (segun el endpoint). Hay validaciones basicas de tipos (intval, floatval).
   - Si en el checkout el carrito esta vacio se evita grabar el pedido.

- Justificacion de decisiones (por que asi y no de otra manera)

   - Elegi PHP + sesiones por rapidez de prototipo: es lo mas directo en XAMPP y es facil de explicar y probar.
   - Use PDO con prepared statements porque es sencillo y seguro para consultas SQL.
   - Mantener el carrito en sesion evita trabajo extra para un prototipo y permite focusear en el flujo de pedidos.
   - El admin con login en archivo es practico para no tener que crear la tabla de admins ahora; pero no es recomendable para produccion.

- Siguientes pasos logicos si queres escalar esto

   - Mover usuarios/admins a la BD y usar password_hash/password_verify.
   - Añadir subida de imagenes para productos y mostrar miniaturas en el menu.
   - Guardar carritos en BD para usuarios logueados y permitir recuperar carritos desde cualquier dispositivo.
   - Agregar tests automaticos para las funciones de BD y para el flujo de checkout.

Eso es todo profe

---

Notas extra - migracion, imagenes y admin en BD

- Si queres soporte para imagenes de productos y administrar admins desde la BD, hay dos scripts nuevos:
   - `tasm_migrate.php` : lo ejecutas desde el navegador (http://localhost/tasm/tasm_migrate.php). Agrega la columna `image` a `tasm_products` si no existe y crea la tabla `tasm_admins`.
   - `tasm_setup_admin.php` : formulario para crear un usuario admin con contraseña segura (usa `password_hash`). Visitalo despues de correr la migracion: http://localhost/tasm/tasm_setup_admin.php

- Una vez creada la tabla `tasm_admins`, el login admin (`tasm_admin_login.php`) intentara validar contra la BD. Si no hay usuario en BD o la tabla no existe, sigue usando el archivo `tasm_admin_conf.php` como respaldo (solo para desarrollo).

Si queres que yo ejecute cambios extra (por ejemplo subir imagenes por defecto, o validar tipos de imagen mas exhaustivos), decime y lo hago.

