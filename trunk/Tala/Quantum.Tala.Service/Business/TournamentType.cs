using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    public enum TournamentType
    {
        /// <summary>
        /// 1, tập sự, chơi vui lấy danh hiệu
        /// </summary>
        Free = 1,
        /// <summary>
        /// 2, nộp phế vào chơi, đánh 1 ván, tiền tươi thóc thật
        /// </summary>
        DeadMatch = 2,
        /// <summary>
        /// 3, giải vô địch theo thời gian, tính điểm từng trận, trao giải cho top player
        /// </summary>
        ChampionShip = 3,

        /// <summary>
        /// 4, thể thức đấu theo nhánh 4 người một bộ đấu
        /// </summary>
        TennisTree = 4
    }
}
