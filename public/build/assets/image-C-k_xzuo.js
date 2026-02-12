const t=t=>{if(!t)return"";if(t.startsWith("http")||t.startsWith("/assets")||t.startsWith("/storage"))return t;const s=t.startsWith("/")?t.substring(1):t;return"/storage/".concat(s)};export{t as g};
