tailwind.config = {
  theme: {
    extend: {
      colors: {
        canvas: "#f3f5f7",
        "canvas-muted": "#e8ecf0",
        surface: "#ffffff",
        ink: "#0b1220",
        "ink-soft": "#1c2738",
        muted: "#5b6577",
        line: "#d7dde6",
        accent: "#0f5c57",
        "accent-soft": "#e6f2f1",
        metal: "#b08d57",
        danger: "#b42318",
        success: "#067647",
      },
      boxShadow: {
        soft: "0 8px 30px rgba(11, 18, 32, 0.06)",
        lift: "0 18px 50px rgba(11, 18, 32, 0.1)",
      },
      borderRadius: {
        xl: "1rem",
        "2xl": "1rem",
        "3xl": "1.5rem",
      },
      fontFamily: {
        sans: ['"IBM Plex Sans Arabic"', "Segoe UI", "Tahoma", "sans-serif"],
      },
    },
  },
};
