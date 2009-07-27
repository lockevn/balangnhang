using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// enum, chỉ ra là người chơi (nhận tiền hay mất tiền) là do thắng, do thua, hay mất gold khi đang chơi (bị ăn chốt, bị nộp gà)
    /// </summary>
    public enum EnumPlayingResult
    {
        /// <summary>
        /// bị ăn chốt, bị nộp gà ...
        /// </summary>
        Nothing,
        /// <summary>
        /// thắng
        /// </summary>
        Win,
        /// <summary>
        /// thua
        /// </summary>
        Lose
    }
}
