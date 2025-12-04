import  { useState, useRef,  } from 'react';
import { Upload, X,  Save, Trash2, GripVertical, Image as ImageIcon, Video } from 'lucide-react';

const GREEN_PRIMARY = '#007853';

// Helper: Formatea minutos a formato HH:MM
const formatTime = (totalMinutes) => {
  const hours = Math.floor(totalMinutes / 60);
  const minutes = Math.floor(totalMinutes % 60);
  const ampm = hours >= 12 ? 'PM' : 'AM';
  const displayHours = hours % 12 || 12;
  return `${displayHours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
};

export default function App() {
  // Estado global
  const [libraryItems, setLibraryItems] = useState([
    { id: 'lib-1', type: 'video', name: 'Intro_Comercial.mp4', duration: 60, color: GREEN_PRIMARY },
    { id: 'lib-2', type: 'image', name: 'Banner_Promo.jpg', duration: 60, color: GREEN_PRIMARY }, // Ajustado default a 60
    { id: 'lib-3', type: 'video', name: 'Entrevista_CEO.mp4', duration: 120, color: GREEN_PRIMARY },
  ]);
  
  const [timelineItems, setTimelineItems] = useState([]);
  const [draggingItem, setDraggingItem] = useState(null);
  
  // Referencias para el timeline
  const timelineRef = useRef(null);
  const scrollContainerRef = useRef(null);
  
  // Configuración del timeline
  const PIXELS_PER_MINUTE = 2; // 1 hora = 120px
  const START_HOUR = 0; // 12 AM
  const TOTAL_HOURS = 24;
  const SNAP_MINUTES = 60; // Configuración de SNAP a 1 HORA
  
  // --- Lógica de Carga de Archivos ---
  const handleFileUpload = (e) => {
    const files = Array.from(e.target.files || []);
    const newItems = files.map((file, index) => ({
      id: `new-${Date.now()}-${index}`,
      type: file.type.startsWith('video') ? 'video' : 'image',
      name: file.name,
      duration: 60, // Duración por defecto 1 hora
      url: URL.createObjectURL(file), // Preview local
      color: GREEN_PRIMARY
    }));
    setLibraryItems([...libraryItems, ...newItems]);
  };

  // --- Lógica Drag & Drop (Inicio) ---
  const handleDragStart = (e, item, source) => {
    setDraggingItem({ ...item, source });
    e.dataTransfer.effectAllowed = 'copyMove';
  };

  // --- Lógica Drop en Timeline ---
  const handleTimelineDrop = (e) => {
    e.preventDefault();
    if (!draggingItem) return;

    const rect = timelineRef.current.getBoundingClientRect();
    const scrollLeft = scrollContainerRef.current.scrollLeft;
    const clickX = (e.clientX - rect.left) + scrollLeft;
    
    // Calcular minuto de inicio basado en pixeles
    let startMinute = Math.floor(clickX / PIXELS_PER_MINUTE);
    
    // Redondear a grid de 1 HORA (60 minutos)
    startMinute = Math.round(startMinute / SNAP_MINUTES) * SNAP_MINUTES;

    const newItem = {
      id: `tl-${Date.now()}`,
      mediaId: draggingItem.id,
      name: draggingItem.name,
      type: draggingItem.type,
      start: startMinute,
      duration: draggingItem.duration || 60, // Asegura mínimo 1 hora
      color: draggingItem.color
    };

    // Validar colisión
    if (!hasCollision(newItem, timelineItems)) {
      setTimelineItems([...timelineItems, newItem]);
    } else {
      alert("¡Espacio ocupado! No puedes soltar un elemento sobre otro.");
    }
    
    setDraggingItem(null);
  };

  const hasCollision = (newItem, currentItems, excludeId = null) => {
    const newEnd = newItem.start + newItem.duration;
    
    return currentItems.some(item => {
      if (item.id === excludeId) return false; // Ignorar el mismo item al redimensionar
      const itemEnd = item.start + item.duration;
      
      // Lógica de superposición
      return (newItem.start < itemEnd && newEnd > item.start);
    });
  };

  // --- Lógica de Redimensionado (Estirar) ---
  const handleResizeStart = (e, item) => {
    e.stopPropagation();
    e.preventDefault(); // Prevenir drag del item padre

    const startX = e.clientX;
    const startDuration = item.duration;

    const onMouseMove = (moveEvent) => {
      const deltaX = moveEvent.clientX - startX;
      const deltaMinutes = Math.floor(deltaX / PIXELS_PER_MINUTE);
      
      // Nueva duración propuesta (minimo 60 mins / 1 hora)
      let newDuration = Math.max(60, startDuration + deltaMinutes);
      
      // Snap a 60 mins (1 hora)
      newDuration = Math.round(newDuration / SNAP_MINUTES) * SNAP_MINUTES;

      const tempItem = { ...item, duration: newDuration };

      if (!hasCollision(tempItem, timelineItems, item.id)) {
         setTimelineItems(prev => prev.map(i => i.id === item.id ? tempItem : i));
      }
    };

    const onMouseUp = () => {
      document.removeEventListener('mousemove', onMouseMove);
      document.removeEventListener('mouseup', onMouseUp);
    };

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
  };

  // --- Eliminar del Timeline ---
  const removeTimelineItem = (id) => {
    setTimelineItems(prev => prev.filter(i => i.id !== id));
  };

  // --- Eliminar de la Librería ---
  const removeLibraryItem = (id) => {
    setLibraryItems(prev => prev.filter(i => i.id !== id));
  };

  return (
    <div className="flex flex-col h-screen bg-gray-50 font-sans text-gray-800">
      
      {/* HEADER */}
      <header className="bg-white border-b px-6 py-4 flex justify-between items-center shadow-sm z-10">
        <h1 className="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <Video className="w-6 h-6" style={{ color: GREEN_PRIMARY }} />
          Gestor de Contenido
        </h1>
        <div className="flex gap-3">
          <button className="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-md font-medium transition">Cancelar</button>
          <button className="px-6 py-2 text-white rounded-md font-medium shadow-md transition hover:opacity-90 flex items-center gap-2"
            style={{ backgroundColor: GREEN_PRIMARY }}>
            <Save className="w-4 h-4" /> Guardar Programación
          </button>
        </div>
      </header>

      {/* MAIN CONTENT */}
      <div className="flex-1 flex flex-col overflow-hidden">
        
        {/* TOP: TIMELINE AREA */}
        <div className="h-1/3 min-h-[250px] bg-gray-900 border-b relative flex flex-col shadow-inner">
          <div className="absolute top-2 left-4 z-10 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-80 pointer-events-none">
            Timeline (24 Horas)
          </div>

          {/* Scroll Container */}
          <div 
            ref={scrollContainerRef}
            className="flex-1 overflow-x-auto overflow-y-hidden relative custom-scrollbar"
            onDragOver={(e) => e.preventDefault()}
            onDrop={handleTimelineDrop}
          >
            <div 
              ref={timelineRef}
              className="relative h-full"
              style={{ width: `${TOTAL_HOURS * 60 * PIXELS_PER_MINUTE}px` }}
            >
              {/* Grid Background & Time Labels */}
              {Array.from({ length: TOTAL_HOURS }).map((_, i) => (
                <div 
                  key={i} 
                  className="absolute top-0 bottom-0 border-l border-gray-700/50"
                  style={{ left: `${i * 60 * PIXELS_PER_MINUTE}px` }}
                >
                  <span className="absolute top-2 left-2 text-gray-400 text-sm font-mono select-none">
                    {formatTime(i * 60)}
                  </span>
                </div>
              ))}

              {/* Timeline Items */}
              {timelineItems.map((item) => (
                <div
                  key={item.id}
                  className="absolute top-1/2 -translate-y-1/2 h-24 rounded-lg shadow-lg group overflow-hidden border border-white/20 hover:z-20 transition-colors"
                  style={{
                    left: `${item.start * PIXELS_PER_MINUTE}px`,
                    width: `${item.duration * PIXELS_PER_MINUTE}px`,
                    backgroundColor: item.color,
                  }}
                >
                  {/* Content */}
                  <div className="p-2 h-full flex flex-col justify-between relative">
                    <div className="flex justify-between items-start">
                      <div className="flex items-center gap-1 text-white text-xs font-bold truncate pr-6">
                        {item.type === 'video' ? <Video size={12}/> : <ImageIcon size={12}/>}
                        <span className="truncate">{item.name}</span>
                      </div>
                      <button 
                        onClick={() => removeTimelineItem(item.id)}
                        className="text-white/70 hover:text-white hover:bg-black/20 rounded p-0.5 absolute top-1 right-1"
                      >
                        <X size={14} />
                      </button>
                    </div>
                    <div className="text-white/80 text-[10px] font-mono mt-1">
                       {formatTime(item.start)} - {formatTime(item.start + item.duration)}
                    </div>
                  </div>

                  {/* Drag Handle (Right Edge) for Resizing */}
                  <div
                    className="absolute right-0 top-0 bottom-0 w-4 cursor-col-resize hover:bg-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    onMouseDown={(e) => handleResizeStart(e, item)}
                  >
                    <GripVertical size={12} className="text-white" />
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* BOTTOM AREA */}
        <div className="flex-1 flex overflow-hidden">
          
          {/* LEFT: MEDIA LIBRARY */}
          <div className="w-1/3 border-r bg-white flex flex-col min-w-[300px]">
            <div className="p-4 border-b bg-gray-50">
              <h2 className="font-semibold text-gray-700 flex items-center gap-2">
                <ImageIcon className="w-4 h-4" /> Librería de Medios
              </h2>
              <p className="text-xs text-gray-500 mt-1">Arrastra elementos al timeline superior.</p>
            </div>
            
            <div className="flex-1 overflow-y-auto p-4 space-y-3">
              {libraryItems.length === 0 && (
                 <div className="text-center py-10 text-gray-400 text-sm italic">
                   No hay videos o imágenes.<br/>Súbelos en el panel derecho.
                 </div>
              )}
              {libraryItems.map((item) => (
                <div
                  key={item.id}
                  draggable
                  onDragStart={(e) => handleDragStart(e, item, 'library')}
                  className="relative p-3 rounded-lg border border-gray-200 shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition-all group bg-white hover:border-[#007853]"
                >
                  <div className="flex gap-3 items-center">
                    {/* Thumbnail Placeholder */}
                    <div 
                      className="w-16 h-12 rounded flex items-center justify-center shrink-0"
                      style={{ backgroundColor: GREEN_PRIMARY }}
                    >
                      {item.type === 'video' ? 
                        <Video className="text-white w-6 h-6" /> : 
                        <ImageIcon className="text-white w-6 h-6" />
                      }
                    </div>
                    <div className="overflow-hidden">
                      <p className="text-sm font-medium text-gray-800 truncate">{item.name}</p>
                      <span className="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full inline-block mt-1 uppercase">
                        {item.type}
                      </span>
                    </div>
                  </div>
                  
                  {/* Delete Button */}
                  <button 
                    onClick={() => removeLibraryItem(item.id)}
                    className="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <Trash2 size={14} />
                  </button>
                </div>
              ))}
            </div>
          </div>

          {/* RIGHT: UPLOAD AREA */}
          <div className="w-2/3 bg-gray-50 p-8 flex flex-col justify-center items-center">
            <div className="w-full max-w-md">
              <label 
                className="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-white hover:bg-gray-50 hover:border-[#007853] transition-colors group"
              >
                <div className="flex flex-col items-center justify-center pt-5 pb-6">
                  <div className="mb-4 p-4 rounded-full bg-gray-50 group-hover:bg-[#007853]/10 transition-colors">
                    <Upload className="w-10 h-10 text-gray-400 group-hover:text-[#007853]" />
                  </div>
                  <p className="mb-2 text-sm text-gray-500 font-medium">Haz clic o arrastra archivos aquí</p>
                  <p className="text-xs text-gray-400">Soporta: MP4, AVI, JPG, PNG</p>
                </div>
                <input 
                  type="file" 
                  className="hidden" 
                  multiple 
                  accept="image/*,video/*"
                  onChange={handleFileUpload}
                />
              </label>
              
              <div className="mt-6 flex gap-4 text-xs text-gray-400 justify-center">
                <span className="flex items-center gap-1"><div className="w-2 h-2 rounded-full bg-[#007853]"></div> Videos permitidos</span>
                <span className="flex items-center gap-1"><div className="w-2 h-2 rounded-full bg-[#007853]"></div> Imágenes permitidas</span>
              </div>
            </div>
          </div>

        </div>
      </div>
      
      {/* CSS Styles for Scrollbar */}
      <style jsx global>{`
        .custom-scrollbar::-webkit-scrollbar {
          height: 12px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
          background: #1f2937; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
          background-color: #4b5563; 
          border-radius: 6px;
          border: 3px solid #1f2937;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
          background-color: #007853;
        }
      `}</style>
    </div>
  );
}