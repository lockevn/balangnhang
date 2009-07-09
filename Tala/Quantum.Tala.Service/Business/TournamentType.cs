using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    public enum TournamentType
    {
        /// <summary>
        /// tập sự, chơi vui lấy danh hiệu
        /// </summary>
        Free,
        /// <summary>
        /// nộp phế vào chơi, đánh 1 ván, tiền tươi thóc thật
        /// </summary>
        DeadMatch,
        /// <summary>
        /// Đấu loại trực tiếp, chia nhánh
        /// </summary>
        TennisTree
    }
}
