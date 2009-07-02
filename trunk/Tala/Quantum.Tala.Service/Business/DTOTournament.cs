using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Giải đấu
    /// </summary>
    public class DTOTournament
    {
        public string Name { get; set; }
        public DateTime FromTime { get; set; }
        public DateTime ToTime { get; set; }
        public bool IsStart { get; set; }
        public bool IsEnabled { get; set; }
        public int MinRequiredPlayer { get; set; }
        public int StartUpPoint { get; set; }
    }
}
