import L from "leaflet";
import "leaflet/dist/leaflet.css";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});

const createPopupContent = (point) => {
  const container = document.createElement("div");
  const name = point && point.name != null ? String(point.name).trim() : "";
  const city = point && point.city != null ? String(point.city).trim() : "";

  if (name) {
    const strong = document.createElement("strong");
    strong.textContent = name;
    container.appendChild(strong);
  }

  if (city) {
    if (name) {
      container.appendChild(document.createElement("br"));
    }
    container.appendChild(document.createTextNode(city));
  }

  return container;
};

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".prcw-bars-directory").forEach((block) => {
    const mapEl = block.querySelector(".prcw-bars-directory__map");
    const filters = block.querySelectorAll(".prcw-bars-directory__filter");
    const cards = block.querySelectorAll(".prcw-bars-directory__card");

    let map = null;
    let markers = [];
    let points = [];

    if (mapEl) {
      try {
        points = JSON.parse(mapEl.getAttribute("data-points") || "[]");
      } catch (e) {
        points = [];
      }

      map = L.map(mapEl).setView([18.2208, -66.5901], 9);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
        maxZoom: 19,
      }).addTo(map);

      markers = (Array.isArray(points) ? points : [])
        .filter((point) => {
          const lat = Number(point && point.lat);
          const lng = Number(point && point.lng);
          return (
            point &&
            point.lat !== "" &&
            point.lng !== "" &&
            Number.isFinite(lat) &&
            Number.isFinite(lng)
          );
        })
        .map((point) => {
          const marker = L.marker([Number(point.lat), Number(point.lng)]).addTo(
            map,
          );
          marker.bindPopup(createPopupContent(point));
          marker.cityName =
            point && point.city != null ? String(point.city) : "";
          return marker;
        });

      if (markers.length) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
      }
    }

    const filterByCity = (city) => {
      cards.forEach((card) => {
        const matches =
          city === "all" || card.getAttribute("data-city") === city;
        card.style.display = matches ? "" : "none";
      });

      if (map && markers.length) {
        const visibleMarkers = markers.filter(
          (m) => city === "all" || m.cityName === city,
        );

        markers.forEach((m) => {
          if (city === "all" || m.cityName === city) {
            if (!map.hasLayer(m)) {
              m.addTo(map);
            }
          } else {
            map.removeLayer(m);
          }
        });

        if (visibleMarkers.length) {
          const group = L.featureGroup(visibleMarkers);
          map.fitBounds(group.getBounds().pad(0.1));
        }
      }
    };

    filters.forEach((btn) => {
      btn.addEventListener("click", () => {
        filters.forEach((b) => b.classList.remove("is-active"));
        btn.classList.add("is-active");
        filterByCity(btn.getAttribute("data-city"));
      });
    });
  });
});
